class LocalCore {
    static initFormComponents() {
        if ($(".textarea-autosize")[0] && autosize($(".textarea-autosize")), $("input-mask")[0] && $(".input-mask").mask(), $("select.select2")[0]) {
            var a = $(".select2-parent")[0] ? $(".select2-parent") : $("body");
            $("select.select2").select2({dropdownAutoWidth: !0, width: "100%", dropdownParent: a})
        }

        $('[type="file"].file').fileinput({'showUpload':false, 'showCancel':false});
        $('[data-toggle="tooltip"]').tooltip();
    }
}