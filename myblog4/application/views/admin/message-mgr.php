<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Amaze UI Admin table Examples</title>
    <meta name="description" content="这是一个 table 页面">
    <meta name="keywords" content="table">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <base href="<?php echo site_url(); ?>">
    <link rel="icon" type="image/png" href="assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <link rel="stylesheet" href="assets/css/amazeui.min.css"/>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a href="http://browsehappy.com/" target="_blank">升级浏览器</a>
    以获得更好的体验！</p>
<![endif]-->

<?php include 'admin-header.php'; ?>

<div class="am-cf admin-main">
    <?php include 'admin-sidebar.php'; ?>

    <!-- content start -->
    <div class="admin-content">

        <div class="am-cf am-padding">
            <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">表格</strong> /
                <small>Table</small>
            </div>
        </div>

        <div class="am-g">
            <div class="am-u-sm-12 am-u-md-6">
                <div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs">
                        <button id="btn-add" type="button" class="am-btn am-btn-default"><span
                                class="am-icon-plus"></span> 新增
                        </button>
                        <button id="btn-del" type="button" class="am-btn am-btn-default btn-delete"><span
                                class="am-icon-trash-o"></span> 删除
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="am-g">
            <div class="am-u-sm-12">
                <table class="am-table am-table-striped am-table-hover table-main">
                    <thead>
                    <tr>
                        <th class="table-check"><input type="checkbox" id='selectall'></th>
                        <th class="table-title">ID</th>
                        <th class="table-id">NAME</th>
                        <th class="table-title">EMAIL</th>
                        <th class="table-type">CONTENT</th>
                        <th class="table-type">TIME</th>
                        <th class="table-set">操作</th>
                    </tr>
                    </thead>
                    <tbody id="tbody">

                    </tbody>
                </table>
                <div class="am-cf" style="text-align: center;">
                    <button type="button" id="loadmore" class="am-btn am-btn-default">加载更多...</button>
                </div>
            </div>

        </div>
    </div>
    <!-- content end -->
</div>

<a href="#" class="am-icon-btn am-icon-th-list am-show-sm-only admin-menu"
   data-am-offcanvas="{target: '#admin-offcanvas'}"></a>

<footer>
    <hr>
    <p class="am-padding-left">© 2014 AllMobilize, Inc. Licensed under MIT license.</p>
</footer>

<script type="text/template" id="tmpl">
    <tr>
        <td><input  type="checkbox" value="<%= message_id%>"/></td>
        <td><%= message_id%></td>
        <td><%= username%></td>
        <td><a href=""><%= email%></a></td>
        <td><%= content%></td>
        <td><%= add_time%></td>
        <td>
            <div class="am-btn-toolbar">
                <div class="am-btn-group am-btn-group-xs">
                    <button data-id="<%= message_id%>" class="am-btn am-btn-default am-btn-xs am-text-secondary btn-edit">
                        <span class="am-icon-pencil-square-o"></span> 回复
                    </button>
                    <button data-id="<%= message_id%>"
                            class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only btn-delete"><span
                            class="am-icon-trash-o"></span> 删除
                    </button>
                </div>
            </div>
        </td>
    </tr>
</script>

<!--[if lt IE 9]>
<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="assets/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->

<!--[if (gte IE 9)|!(IE)]><!-->
<script src="assets/js/jquery.min.js"></script>
<!--<![endif]-->
<script src="assets/js/amazeui.min.js"></script>
<script src="assets/js/underscore.js"></script>

<script src="assets/js/app.js"></script>
<script>

    $(function () {
        $('#tbody').on('click', '.btn-delete', function () {
            var that = this;
            var messageId = $(this).data('id');
            if (confirm('确定是否删除记录，不可恢复!?')) {
                //location.href = 'admin/delete_blog?blog_id='+blogId;
                $.get('admin/delete_message?message_id=' + messageId, function (res) {
                    if (res == 'success') {
                        $(that).parents('tr').remove();
                    } else {
                        alert('当前操作失败!');
                    }
                }, 'text');
            }
        });

        $('#btn-add').on('click', function () {
            location.href = 'welcome/contact';
        });

        $('#tbody').on('click', '.btn-edit', function(){
            var messageId = $(this).data('id');
            location.href = 'admin/reply_edit?message_id=' + messageId;
        });

        var page = 1, isEnd = false;

        function loadData() {

            $.get('admin/get_message?page=' + page++, function (res) {
                isEnd = res.isEnd;
                setTimeout(function () {
                    for (var i = 0; i < res.data.length; i++) {
                        var message = res.data[i];
                        $('#tbody').append(_.template($('#tmpl').html())(message));
                    }

                    $('#loadmore').removeAttr('disabled').html('加载更多...');
                }, 2000)
            }, 'json');
        }

        loadData();

        $('#loadmore').on('click', function () {
            if (isEnd) {
                alert('数据加载完毕!');
            } else {
                this.disabled = 'disabled';
                this.innerHTML = '加载中...';
                loadData();
            }
        });
        //全选按钮
        $('#selectall').on('click',function(){

            if($(this).prop('checked')){

                $('input[type="checkbox"]').each(function(){
                    $(this).prop("checked",true);
                });
            }else{

                $('input[type="checkbox"]').each(function(){
                    $(this).prop("checked",false);
                });
            }

        });
        //选中删除
        $('#btn-del').on('click',function(){
            var message_ids= $('input:checked').not('#selectall');
            var anum=new Array();
            for(var i=0;i<message_ids.length;i++){
                anum[i]=message_ids.eq(i).val();
            }
            if (confirm('确定是否删除记录，不可恢复!?')) {
                $.get('admin/delete_all_message', {
                    'anums': anum
                }, function (res) {
                    if (res == "success") {

                        message_ids.parents('tr').remove();

                    }

                })
            }
        });


    });
</script>
</body>
</html>
