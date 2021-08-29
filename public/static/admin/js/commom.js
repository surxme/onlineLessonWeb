function verifyinput() {
    var ok = true;
    var tips = '';
    $('.require').each(function(){
        var $this = $(this);
        if($this.val() == '' && ok){
            ok = false;
            layer.msg( $this.data('name')+'不能为空');
        }
    });

    $('.req_checkbox').each(function(){
        var $this = $(this);
        if($this.find('input[type="checkbox"]:checked').length <=0 && ok){
            ok = false;
            Tip( $this.data('name')+'不能为空');
        }
    });

    return ok;
}
