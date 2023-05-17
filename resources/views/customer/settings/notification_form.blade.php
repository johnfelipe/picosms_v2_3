<table class="w-100">
    <tr>
        <td><label>{{trans('customer.email_notification')}}</label></td>
        <td>
            <div class="form-group mt-2">
                <div class="custom-control custom-switch">
                    <input {{isset($customer_settings['email_notification']) && $customer_settings['email_notification']=='true'?'checked':''}} type="checkbox" class="custom-control-input" id="notification_switch">
                    <label class="custom-control-label" for="notification_switch"></label>
                </div>
            </div>
        </td>
    </tr>
</table>
