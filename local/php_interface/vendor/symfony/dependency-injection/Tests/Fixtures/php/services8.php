<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final since Symfony 3.3
 */
class ProjectServiceContainer extends Container
{
    private $parameters;
    private $targetDirs = [];

    /**
     * @internal but protected for BC on cache:clear
     */
    protected $privates = [];

    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services = $this->privates = [];

        $this->aliases = [];
    }

    public function reset()
    {
        $this->privates = [];
        parent::reset();
    }

    public function compile()
    {
        throw new LogicException( 'You cannot compile a dumped container that was already compiled.' );
    }

    public function isCompiled()
    {
        return true;
    }

    public function getRemovedIds()
    {
        return [
            'Psr\\Container\\ContainerInterface' => true,
            'Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
        ];
    }

    public function getParameter( $name )
    {
        $name = (string)$name;

        if ( !( isset( $this->parameters[ $name ] ) || isset( $this->loadedDynamicParameters[ $name ] ) || array_key_exists( $name,
                $this->parameters ) ) )
        {
            throw new InvalidArgumentException( sprintf( 'The parameter "%s" must be defined.', $name ) );
        }
        if ( isset( $this->loadedDynamicParameters[ $name ] ) )
        {
            return $this->loadedDynamicParameters[ $name ] ? $this->dynamicParameters[ $name ] : $this->getDynamicParameter( $name );
        }

        return $this->parameters[ $name ];
    }

    public function hasParameter( $name )
    {
        $name = (string)$name;

        return isset( $this->parameters[ $name ] ) || isset( $this->loadedDynamicParameters[ $name ] ) || array_key_exists( $name,
                $this->parameters );
    }

    public function setParameter( $name, $value )
    {
        throw new LogicException( 'Impossible to call set() on a frozen ParameterBag.' );
    }

    public function getParameterBag()
    {
        if ( null === $this->parameterBag )
        {
            $parameters = $this->parameters;
            foreach ( $this->loadedDynamicParameters as $name => $loaded )
            {
                $parameters[ $name ] = $loaded ? $this->dynamicParameters[ $name ] : $this->getDynamicParameter( $name );
            }
            $this->parameterBag = new FrozenParameterBag( $parameters );
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [];
    private $dynamicParameters = [];

    /**
     * Computes a dynamic parameter.
     *
     * @param string $name The name of the dynamic parameter to load
     *
     * @return mixed The value of the dynamic parameter
     *
     * @throws InvalidArgumentException When the dynamic parameter does not exist
     */
    private function getDynamicParameter( $name )
    {
        throw new InvalidArgumentException( sprintf( 'The dynamic parameter "%s" must be defined.', $name ) );
    }

    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return [
            'foo' => 'bar',
            'baz' => 'bar',
            'bar' => 'foo is %foo bar',
            'escape' => '@escapeme',
            'values' => [
                0 => true,
                1 => false,
                2 => null,
                3 => 0,
                4 => 1000.3,
                5 => 'true',
                6 => 'false',
                7 => 'null',
            ],
            'binary' => '����',
            'binary-control-char' => 'This is a Bell char ',
        ];
    }
}
