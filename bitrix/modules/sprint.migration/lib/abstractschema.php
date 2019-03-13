<?php

namespace Sprint\Migration;

abstract class AbstractSchema
{
    use OutTrait;

    private $name;

    /** @var VersionConfig */
    private $versionConfig = null;

    private $queue = array();

    protected $params = array();

    protected $testMode = 0;

    protected $info = array(
        'title' => '',
    );

    private $enabled = false;

    private $filecache = array();

    abstract public function export();

    abstract public function import();

    abstract protected function initialize();

    abstract public function outDescription();

    abstract public function getMap();

    public function __construct(VersionConfig $versionConfig, $name, $params = array()) {
        $this->versionConfig = $versionConfig;
        $this->name = $name;
        $this->params = $params;
        $this->enabled = $this->isBuilderEnabled();

        $this->initialize();
    }

    protected function isBuilderEnabled() {
        //your code

        return false;
    }

    public function setTestMode($testMode = 1) {
        $this->testMode = ($testMode) ? 1 : 0;
    }

    public function getName() {
        return $this->name;
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function isModified() {
        $opt = strtolower('schema_' . $this->getName());
        $oldhash = Module::getDbOption($opt);

        $data = $this->loadSchemas($this->getMap());
        $newhash = md5(serialize($data));
        return ($newhash != $oldhash);
    }

    public function setModified() {
        $data = $this->loadSchemas($this->getMap());
        $newhash = md5(serialize($data));

        $opt = strtolower('schema_' . $this->getName());
        Module::setDbOption($opt, $newhash);
    }

    protected function setTitle($title = '') {
        $this->info['title'] = $title;
    }

    public function getTitle() {
        return $this->info['title'];
    }

    public function outTitle($fullname = true) {
        $title = ($fullname) ? $this->getName() . ' (' . $this->getTitle() . ')' : $this->getTitle();
        if ($this->isModified()) {
            $this->out('[new]' . $title . '[/]');
        } else {
            $this->out('[installed]' . $title . '[/]');
        }
    }

    protected function getSchemaDir($relative = false) {
        return $this->getVersionConfig()->getSiblingDir('schema', $relative, $this->getVersionConfig()->getName());
    }

    protected function getSchemaSubDir($name, $relative = false) {
        $dir = $this->getSchemaDir() . $name;
        return ($relative) ? Module::getRelativeDir($dir) : $dir;
    }

    protected function getSchemaFile($name, $relative = false) {
        $file = $this->getSchemaDir() . $name . '.json';
        return ($relative) ? Module::getRelativeDir($file) : $file;
    }

    protected function saveSchema($name, $data) {
        $file = $this->getSchemaFile($name);

        $dir = pathinfo($file, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            mkdir($dir, BX_DIR_PERMISSIONS, true);
        }

        file_put_contents($file,
            json_encode($data, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT)
        );
    }

    public function deleteSchemaFiles() {
        $names = $this->getSchemas($this->getMap());
        foreach ($names as $name) {
            $file = $this->getSchemaFile($name);
            unlink($file);
        }
    }

    public function getSchemaFiles() {
        $result = array();

        $names = $this->getSchemas($this->getMap());
        foreach ($names as $name) {
            $result[] = $this->getSchemaFile($name, true);
        }

        return $result;
    }

    protected function getSchemas($map) {
        $map = is_array($map) ? $map : array($map);
        $result = array();

        foreach ($map as $path) {
            $dir = $this->getSchemaSubDir($path);
            $file = $this->getSchemaFile($path);

            if (is_dir($dir)) {
                /* @var $item \SplFileInfo */
                $items = new \DirectoryIterator($dir);
                foreach ($items as $item) {
                    if ($item->isFile() && $item->getExtension() == 'json') {
                        $result[] = $path . $item->getBasename('.json');
                    }
                }
            }

            if (is_file($file)) {
                $result[] = $path;
            }
        }

        return $result;
    }

    protected function loadSchema($name, $merge = array()) {
        if (!isset($this->filecache[$name])) {
            $this->filecache[$name] = $this->loadSchemaFile($name);
        }

        return array_merge($merge, $this->filecache[$name]);
    }


    private function loadSchemaFile($name) {
        $file = $this->getSchemaFile($name);

        if (!is_file($file)) {
            return array();
        }

        $json = file_get_contents($file);
        $json = json_decode($json, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            return array();
        }

        if (!is_array($json)) {
            return array();
        }

        return $json;
    }


    protected function loadSchemas($map, $merge = array()) {
        $names = $this->getSchemas($map);
        $schemas = array();
        foreach ($names as $name) {
            $schemas[$name] = $this->loadSchema($name, $merge);
        }
        return $schemas;
    }

    public function getQueue() {
        return $this->queue;
    }

    protected function addToQueue($method, $var1 = null, $var2 = null) {
        $args = func_get_args();
        $method = array_shift($args);
        $this->queue[] = array($method, $args);
    }

    public function executeQueue($item) {
        if (method_exists($this, $item[0])) {
            call_user_func_array(array($this, $item[0]), $item[1]);
        } else {
            $this->outError('method %s not found', $item[0]);
        }
    }

    protected function getVersionConfig() {
        return $this->versionConfig;
    }
}