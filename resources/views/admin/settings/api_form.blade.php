
@php

$signalwire=json_decode(get_settings('signalwire'));
$twilio=json_decode(get_settings('twilio'));
$nexmo=json_decode(get_settings('nexmo'));
$telnyx=json_decode(get_settings('telnyx'));
$plivo=json_decode(get_settings('plivo'));
@endphp

<div class="form-group">
    <label for="gateway">@lang('admin.settings.gateway')</label>
    <select required class="form-control" name="gateway" id="gateway">
        <option value="signalwire">Signalwire</option>
        <option value="twilio">Twilio</option>
        <option value="nexmo">Nexmo</option>
        <option value="telnyx">Telnyx</option>
        <option value="plivo">Plivo</option>
    </select>
</div>
<div id="signalwire_section" class="api-section">
    <div class="form-group">
        <label for="project_id">@lang('admin.settings.project_id')</label>
        <input required value="{{isset($signalwire->sw_project_id)?$signalwire->sw_project_id:''}}" class="form-control" type="text" name="sw_project_id" id="project_id">
    </div>

    <div class="form-group">
        <label for="space_url">@lang('admin.settings.space_url')</label>
        <input required value="{{isset($signalwire->sw_space_url)?$signalwire->sw_space_url:''}}" class="form-control" type="text" name="sw_space_url" id="space_url">
    </div>

    <div class="form-group">
        <label for="sw_token">@lang('admin.settings.token')</label>
        <input required value="{{isset($signalwire->sw_token)?$signalwire->sw_token:''}}"  class="form-control" type="text" name="sw_token" id="sw_token">
    </div>
</div>
<div id="twilio_section" style="display: none" class="api-section">
    <div class="form-group">
        <label for="tw_sid">@lang('admin.settings.sid')</label>
        <input required value="{{isset($twilio->tw_sid)?$twilio->tw_sid:''}}" class="form-control" type="text" name="tw_sid" id="tw_sid">
    </div>
    <div class="form-group">
        <label for="tw_token">@lang('admin.settings.token')</label>
        <input required value="{{isset($twilio->tw_token)?$twilio->tw_token:''}}" class="form-control" type="text" name="tw_token" id="tw_token">
    </div>
</div>
<div id="nexmo_section" style="display: none" class="api-section">

    <div class="form-group">
        <label for="nx_api_key">@lang('admin.settings.api_key')</label>
        <input required value="{{isset($nexmo->nx_api_key)?$nexmo->nx_api_key:''}}" class="form-control" type="text" name="nx_api_key" id="nx_api_key">
    </div>

    <div class="form-group">
        <label for="nx_api_secret">@lang('admin.settings.api_secret')</label>
        <input required value="{{isset($nexmo->nx_api_secret)?$nexmo->nx_api_secret:''}}" class="form-control" type="text" name="nx_api_secret" id="nx_api_secret">
    </div>
</div>

<div id="telnyx_section" style="display: none" class="api-section">

    <div class="form-group">
        <label for="tl_api_key">@lang('admin.settings.api_key')</label>
        <input required value="{{isset($telnyx->tl_api_key)?$telnyx->tl_api_key:''}}" class="form-control" type="text" name="tl_api_key" id="tl_api_key">
    </div>
</div>
<div id="plivo_section" style="display: none" class="api-section">

    <div class="form-group">
        <label for="pl_auth_id">@lang('admin.settings.auth_id')</label>
        <input required value="{{isset($plivo->pl_auth_id)?$plivo->pl_auth_id:''}}" class="form-control" type="text" name="pl_auth_id" id="pl_auth_id">
    </div>
    <div class="form-group">
        <label for="pl_auth_token">@lang('admin.settings.auth_token')</label>
        <input required value="{{isset($plivo->pl_auth_id)?$plivo->pl_auth_token:''}}" class="form-control" type="text" name="pl_auth_token" id="pl_auth_token">
    </div>
</div>

