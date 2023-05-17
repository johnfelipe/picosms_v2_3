<div class="form-group">
    <label for="title">@lang('admin.form.title')</label>
    <input value="{{isset($plan)?$plan->title:old('title')}}" type="text" name="title" class="form-control" id="title"
           placeholder="@lang('admin.form.input.title')">
</div>

<div class="form-group">
    <label for="limit">@lang('admin.form.limit')</label>
    <input value="{{isset($plan)?$plan->sms_limit:old('limit')}}" type="number" name="limit" class="form-control" id="limit"
           placeholder="@lang('admin.form.input.limit')">
</div>

<div class="form-group">
    <label for="price">@lang('admin.form.price')</label>
    <input value="{{isset($plan)?$plan->price:old('price')}}" type="number" name="price" class="form-control" id="price"
           placeholder="@lang('admin.form.input.price')">
</div>

<div class="form-group">
    <label for="status">@lang('admin.form.status')</label>
    <select class="form-control" name="status" id="status">
        <option {{isset($plan) && $plan->status=='Active'?'selected':(old('status')=='Active'?'selected':'')}} value="active">Active</option>
        <option {{isset($plan) && $plan->status=='Inactive'?'selected':(old('status')=='Inactive'?'selected':'')}} value="inactive">Inactive</option>
    </select>
</div>
