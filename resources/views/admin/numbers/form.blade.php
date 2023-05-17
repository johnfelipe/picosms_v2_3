<div class="form-group">
    <label for="number">@lang('admin.form.number')</label>
    <input {{isset($number)?'readonly':''}} value="{{isset($number)?$number->number:old('number')}}" type="text" name="number" class="form-control" id="number"
           placeholder="Enter number">
</div>
<div class="form-group">
    <label for="platform">@lang('admin.form.platform')</label>
    <select {{isset($number)?'disabled':''}} class="form-control" name="from" id="from">
        <option {{isset($number) && $number->from=='signalwire'?'selected':(old('from')=='signalwire'?'selected':'')}} value="signalwire">Signalwire</option>
        <option {{isset($number) && $number->from=='twilio'?'selected':(old('from')=='twilio'?'selected':'')}} value="twilio">Twilio</option>
        <option {{isset($number) && $number->from=='nexmo'?'selected':(old('from')=='nexmo'?'selected':'')}} value="nexmo">Nexmo</option>
        <option {{isset($number) && $number->from=='telnyx'?'selected':(old('from')=='telnyx'?'selected':'')}} value="telnyx">Telnyx</option>
        <option {{isset($number) && $number->from=='plivo'?'selected':(old('from')=='plivo'?'selected':'')}} value="plivo">Plivo</option>
    </select>
</div>
<div class="form-group">
    <label for="purch_price">@lang('admin.form.purchase_price')</label>
    <input value="{{isset($number)?$number->purch_price:old('purch_price')}}" type="number" name="purch_price" class="form-control" id="purch_price"
           placeholder="@lang('admin.form.input.purchase_price')">
</div>
<div class="form-group">
    <label for="sell_price">@lang('admin.form.sell_price')</label>
    <input value="{{isset($number)?$number->sell_price:old('sell_price')}}" type="number" name="sell_price" class="form-control" id="sell_price"
           placeholder="@lang('admin.form.input.sell_price')">
</div>

<div class="form-group">
    <label for="status">@lang('admin.form.status')</label>
    <select class="form-control" name="status" id="status">
        <option {{isset($number) && $number->status=='Active'?'selected':(old('status')=='Active'?'selected':'')}} value="active">Active</option>
        <option {{isset($number) && $number->status=='Inactive'?'selected':(old('status')=='Inactive'?'selected':'')}} value="inactive">Inactive</option>
    </select>
</div>
