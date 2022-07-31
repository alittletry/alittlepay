var iframe = function(){

    /**
     * 页面load
     */
    var build = function($title,$url,$param) {
        $width = $param.hasOwnProperty("width") ? $param['width'] : "100%";
        $height = $param.hasOwnProperty("height") ? $param['height'] : "600px";
        $footer = $param.hasOwnProperty("footer") ? $param['footer'] : 'block';
        jQuery('body').prepend(
            '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" data-backdrop="static">\n' +
            '    <div class="modal-dialog modal-lg" role="document">\n' +
            '        <div class="modal-content">\n' +
            '            <div class="modal-header" style="">\n' +
            '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '                <h4 class="modal-title" id="exampleModalLabel">'+$title+'</h4>\n' +
            '            </div>\n' +
            '            <div class="modal-body" style="width: '+$width+';height: '+$height+';">\n' +
            '              <iframe src="'+$url+'" style="width: 100%;border: 0px;height: 100%;"></iframe>'+
            '            </div>\n' +
            '                <div class="modal-footer" style="display: '+$footer+';">\n' +
            '                  <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>\n' +
            '                </div>'+
            '        </div>\n' +
            '    </div>\n' +
            '</div>'
        );
        $('#myModal').on('hide.bs.modal', function () {
            $('#myModal').remove();
        });
        // $('#myModal').on("show.bs.modal", function () {
        //     $(this).draggable();
        //     $(this).css("scroll", "false");
        // });
    };

    var open = function ($title,$url,$param={}) {
        build($title,$url,$param);
        $("#myModal").modal("show");
    };

    var close = function () {
        $("#myModal").modal("hide");
    };

    return {
        // 页面加载动画
        createIframe : function ($title,$url,$param={}) {
            open($title,$url,$param);
        }
    };
}();