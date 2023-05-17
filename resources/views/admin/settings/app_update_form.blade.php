<div class="form-group">
    <label for="name">@lang('admin.form.input.app_name')</label>
    <input value="{{get_settings('app_name')}}" type="text" name="app_name" class="form-control" id="app_name"
           placeholder="@lang('admin.form.input.app_name')">
</div>

<div class="form-group">
    <label for="favicon">@lang('admin.form.input.favicon')</label><img class="img-demo-setting" src="{{asset('uploads/'.get_settings('app_favicon'))}}" alt="">
    <div class="input-group">
        <div class="custom-file">
            <input name="favicon" type="file" class="custom-file-input" id="favicon">
            <label class="custom-file-label" for="favicon">@lang('admin.form.input.choose_file')</label>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="logo">@lang('admin.form.input.logo')</label> <img class="img-demo-setting" src="{{asset('uploads/'.get_settings('app_logo'))}}" alt="">
    <div class="input-group">
        <div class="custom-file">
            <input name="logo" type="file" class="custom-file-input" id="logo">
            <label class="custom-file-label" for="logo">@lang('admin.form.input.choose_file')</label>
        </div>
    </div>
</div>

