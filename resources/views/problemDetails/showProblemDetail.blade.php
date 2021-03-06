@extends('layouts.app')

@section('extendedHead')
    {{--<meta name="csrf-token" content="{{ csrf_token() }}"/>--}}
    <link href="{{URL::asset('assets/global/plugins/bootstrap-toastr/toastr.min.css')}}" rel="stylesheet"
          type="text/css">
@endsection

@section('pageScript')
    <script src="{{URL::asset('assets/global/plugins/bootstrap-toastr/toastr.min.js')}}"
            type="text/javascript"></script>
	<script src="{{URL::asset('js/codehub/editor_init.js')}}" type="text/javascript"></script>
	<script src="{{URL::asset('js/codehub/editor_submit.js')}}" type="text/javascript"></script>
	<script src="{{URL::asset('js/codehub/editor_timer.js')}}" type="text/javascript"></script>
	<script src="{{URL::asset('js/codehub/editor_back_button.js')}}" type="text/javascript"></script>
	<script>
		var hide=false;
        $('#hide-btn').click(function(){
            $('#problemTitle').toggle(300);
			hide = !hide;
			if (hide) $('#hide-btn').html("<b><i class=\"fa fa-angle-down\"></i> Hiện đề bài</b>");
			else $('#hide-btn').html("<b><i class=\"fa fa-angle-up\"></i> Ẩn đề bài</b>");
        })
    </script>
@endsection

@section('script')
@if ($problem != null)
    <script type="text/javascript">
        $('#expand-button').click(function (e) {
            $('#editor-box').toggleClass('fullscreen');
            $('#problem-content').toggle();
        });
    </script>
    <script src="{{ URL::asset('js/ace-builds/src-min-noconflict/ace.js') }}" type="text/javascript"
            charset="utf-8"></script>
    <style>
        .readonly-highlight {
            /*background-color: gainsboro;*/
            background-color: grey;
            opacity: 0.2;
            position: absolute;
        }
    </style>
    <?php
        $templateCode = $problem->templateCode;
        if($templateCode == null){
            $templateCode = "''";
        } else{
            $templateCode = json_encode($templateCode);
        }


    ?>
    <script>
		setupPrepairEditor();
        setupTemplateCode(<?=$templateCode?>);
		@if ($isExam)
			setBackButtonLocation("{{ URL('/exams/'.$examId) }}");
			setupSubmitProblem('{{url('/submitPostAjax')}}', -1, {{$examId}},
												{{$problem->problemId}}, '{{$problem->problemCode}}');
			startTimer({{$remainTime}});
		@else
			setBackButtonLocation("{{ URL('/my-courses/'.$courseId.'/problems') }}");
			setupSubmitProblem('{{url('/submitPostAjax')}}', {{$courseId}}, -1,
												{{$problem->problemId}}, '{{$problem->problemCode}}');
		@endif
		setupSubmissionTable('{{url(Request::path().'/submissionTable')}}');
    </script>
    <script>
		var sourceToCopy = "";
        function showSource(source) {
            $('#sourceText')[0].innerText = source;
			//sourceToCopy = decodeURI(source);
			sourceToCopy = source;
        }
		
		$("#copybtn").click(function(){
			copyTextToClipboard(sourceToCopy);
			toastr.success("Copied code!", "");
		});
		
		$("#editorcopybtn").click(function(){
			copyTextToClipboard($('#source_code').val());
			$("#editorcopybtn").html("Copied!");
			setTimeout(function() {$("#editorcopybtn").html("COPY ALL")}, 1000);
		});
		
		function copyTextToClipboard(text) {
			var textArea = document.createElement("textarea");
			textArea.style.position = 'fixed';
			textArea.style.top = 0;
			textArea.style.left = 0;
			textArea.style.width = '2em';
			textArea.style.height = '2em';
			textArea.style.padding = 0;
			textArea.style.border = 'none';
			textArea.style.outline = 'none';
			textArea.style.boxShadow = 'none';
			textArea.style.background = 'transparent';
			textArea.value = text;
			document.body.appendChild(textArea);
			textArea.select();
			try {
				var successful = document.execCommand('copy');
				var msg = successful ? 'successful' : 'unsuccessful';
				console.log('Copying text command was ' + msg);
			} catch (err) {
				console.log('Oops, unable to copy');
			}
			document.body.removeChild(textArea);
		}

    </script>
@endif
@stop
@section('content')

@if ($problem == null)
	<h1><b>ACCESS DENIED</b></h1>
	<h3>You're not a member of this course</h3>
	<h3>If you want to try this problem, please enroll in this course: <br/><a href="{{url('/all-courses')}}">{{$courseName}}</a></h3>
@else
    <div id="sourceModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Mã nguồn</h4>
                </div>
                <!-- dialog body -->
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <pre id="sourceText"></pre>
                </div>
                <!-- dialog buttons -->
                <div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" id="copybtn">Copy tất cả</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">Thoát</button>
				</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div id="pl_pr" class="portlet light">
                <div class="portlet-title">

                    <div class="caption">
						<button type="button" id="backbtn" class="btn btn-primary"><i class="fa fa-arrow-left"></i> QUAY LẠI </button>
						&nbsp&nbsp
                        <span class="caption-subject font-blue bold uppercase">
                            {{$problem->problemCode}}
                        </span>
                    </div>
                </div>
                <div id="problemTitle" class="portlet-body">
                    <div class="box" id="problem-content" style="min-height: 10px;">
                        <div style="background: #E0E0E0; margin-top: 10px; font-weight: bold">Yêu cầu đề bài</div>
                        <div class="box-content" style="text-align: justify; font-family: monospace;">
                            {!! $problem->content !!}
                        </div>
                        <div>
                            <div style="width: 45%; float: left">
                                <div style="background: #E0E0E0; margin-top: 10px; font-weight: bold">Input
                                </div>
                                <div>{!! $problem->inputDescription !!}</div>
                            </div>
                            <div style="width: 45%; float: right">
                                <div style="background: #E0E0E0; margin-top: 10px; font-weight: bold">Output
                                </div>
                                <div>{!! $problem->outputDescription !!}</div>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div><br/>
                </div>				
				<center>
					<span id="hide-btn" style="background-color: #eee;" class="btn"><b><i class="fa fa-angle-up"></i> Ẩn đề bài</b></span>
				</center>
            </div>


        </div>
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-body">
				@if ($isExam)
					<div style="float: right;font-family: inherit;font-weight: bold; color: cornflowerblue;">
                        <span id="countDownTimer">Đang tải đồng hồ...</span>
                    </div>
				@endif
                    <div class="box">
                        <div id="mytabs" role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class=""><a href="#editor-box" aria-controls="editor-box"
                                                                    role="tab"
                                                                    data-toggle="tab" aria-expanded="false">Mã nguồn</a>
                                </li>
                                <li role="presentation" class=""><a href="#result" aria-controls="submit" role="tab"
                                                                    data-toggle="tab" aria-expanded="false">Kết quả</a>
                                </li>
								<li role="presentation" class=""><a href="#debai" aria-controls="submit" role="tab"
                                                                    data-toggle="tab" aria-expanded="false">Đề bài</a>
                                </li>
                            </ul>
                            <div class="tab-content ">
                                <div role="tabpanel"
                                     class="tab-pane {{Session::get('is_submitted') == true ? '' : 'active'}}"
                                     id="editor-box">

                                    <form id="frmSubmit" onsubmit="return false">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="panel">
                                            <div class="box">
                                                <div class="box-header">

                                                </div>
                                                <div class="box-content">
                                                    <div class="form-group" hidden>
                                                        <textarea class="form-control" name="source_code"
                                                                  id="source_code">

                                                        </textarea>
                                                    </div>
                                                    <div id="editor"></div>
                                                </div>
                                            </div>
                                            <div class="form-group" style="margin-top: 5px">
                                                <div class="pull-left" style="width:80px; margin-right: 5px;">
                                                    <select class="form-control" name="language" id="language"
                                                            onchange="changeLanguage()">
                                                        <option value="Cpp">C++</option>
                                                        <option value="C">C</option>
                                                        <option value="Java">Java</option>
                                                    </select>
                                                </div>
                                                <div class="pull-right">
													<button class="btn btn-primary" type="button" id="editorcopybtn"
														style="margin-right: 2px;">
                                                        COPY
                                                    </button>
                                                    <button class="btn btn-primary" type="submit"
                                                            id="submit-button">
                                                        NỘP BÀI
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>

                                </div>
                                <div role="tabpanel"
                                     class="tab-pane {{Session::get('is_submitted') == true ? 'active' : ''}}"
									 id="result">
                                    <div id="ajaxDemoContent"></div>
                                    {{--@include(url('/'))--}}
                                </div>
								<div role="tabpanel" id="debai">
                                    <div class="box-content" style="text-align: justify; font-family: monospace;">
                                        {!! $problem->content !!}
                                    </div>
                                    <div>
                                        <div style="width: 45%; float: left">
                                            <div style="background: #E0E0E0; margin-top: 10px; font-weight: bold">Input
                                            </div>
                                            <div>{!! $problem->inputDescription !!}</div>
                                        </div>
                                        <div style="width: 45%; float: right">
                                            <div style="background: #E0E0E0; margin-top: 10px; font-weight: bold">Output
                                            </div>
                                            <div>{!! $problem->outputDescription !!}</div>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif
@endsection