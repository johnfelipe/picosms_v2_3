<div class="form-group">
    <label for="name">@lang('customer.group_name') *</label>
    <input value="{{isset($group) && $group->name?$group->name:old('name')}}" type="text" name="name"
           class="form-control" id="name"
           placeholder="@lang('customer.group_name')">
</div>

<div class="form-group">
    <label for="contacts">@lang('customer.contacts')</label>
    <select name="contact_ids[]" class="select2" multiple="multiple" data-placeholder="Select a contact" style="width: 100%;" id="contacts">
        @foreach($contacts as $contact)
            <option @isset($groupContactIds) {{in_array($contact->id,$groupContactIds)?'selected':''}}  @endisset value="{{$contact->id}}">{{$contact->number}} {{$contact->first_name?'('.$contact->first_name.' '.$contact->last_name.')':''}}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="contact_csv">@lang('customer.upload_csv')</label>
    <input type="file" class="form-group" name="contact_csv" id="contact_csv">
    <div class="float-right"><a target="_blank" href="{{route('customer.download.sample',['type'=>'group'])}}" class="text-muted">@lang('customer.download_sample')</a></div>
</div>


<div class="form-group">
    <label for="status">@lang('customer.status')</label>
    <select name="status" id="status" class="form-control">
        <option {{isset($group) && $group->status=='active'?'selected':''}} value="active">Active</option>
        <option {{isset($group) && $group->status=='inactive'?'selected':''}} value="inactive">Inactive</option>
    </select>
</div>
