<div class="form-group">
    <label for="number">@lang('customer.number')</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend d-none">
            <select {{isset($contact)?'disabled':''}} class="form-control" name="contact_dial_code" id="contact_dial_code">
                @foreach(getCountryCode() as $key=>$code)
                    <option {{isset($contact) && $contact->contact_dial_code=="+".$code['code']?'selected':''}} value="+{{$code['code']}}">+{{$code['code']}}</option>
                @endforeach
            </select>
        </div>
        <input {{isset($contact)?'readonly':''}} value="{{isset($contact)?$contact->number:old('number')}}" type="text" name="number" class="form-control" id="number"
               placeholder="Enter number">
    </div>


</div>

<div class="form-group">
    <label for="first_name">@lang('customer.first_name')</label>
    <input value="{{isset($contact) && $contact->first_name?$contact->first_name:old('first_name')}}" type="text" name="first_name" class="form-control" id="first_name"
           placeholder="@lang('customer.first_name')">
</div>

<div class="form-group">
    <label for="last_name">@lang('customer.last_name')</label>
    <input value="{{isset($contact) && $contact->last_name?$contact->last_name:old('last_name')}}" type="text" name="last_name" class="form-control" id="last_name"
           placeholder="@lang('customer.last_name')">
</div>

<div class="form-group">
    <label for="email">@lang('customer.email')</label>
    <input value="{{isset($contact) && $contact->email? $contact->email:old('email')}}" type="text" name="email" class="form-control" id="email"
           placeholder="@lang('customer.email')">
</div>

<div class="form-group">
    <label for="company">@lang('customer.company')</label>
    <input value="{{isset($contact) && $contact->company?$contact->company:old('company')}}" type="text" name="company" class="form-control" id="company"
           placeholder="@lang('customer.company')">
</div>
