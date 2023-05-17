<div class="form-group">
    <label for="name">@lang('customer.keyword') *</label>
    <input value="{{isset($keyword) && $keyword->keyword?$keyword->keyword:old('keyword')}}" type="text" name="keyword"
           class="form-control" id="keyword"
           placeholder="@lang('customer.keyword')">
</div>

<div class="form-group">
    <label for="phone_number">@lang('customer.phone_number')</label>
    <select name="contact_ids[]" class="select2" multiple="multiple" data-placeholder="Select a contact" style="width: 100%;" id="contacts">
        @foreach($contacts as $contact)
            <option @isset($keywordContactIds) {{in_array($contact->id,$keywordContactIds)?'selected':''}}  @endisset value="{{$contact->id}}">{{$contact->number}} {{$contact->first_name?'('.$contact->first_name.' '.$contact->last_name.')':''}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="status">@lang('customer.status')</label>
    <select name="status" id="status" class="form-control">
        <option {{isset($keyword) && $keyword->status=='active'?'selected':''}} value="active">Active</option>
        <option {{isset($keyword) && $keyword->status=='inactive'?'selected':''}} value="inactive">Inactive</option>
    </select>
</div>
