<div class="crm-block crm-form-block crm-page-form-block">
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
 
<fieldset>
    <table id="update_settings" >
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Value</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        
        <tbody>
            {foreach from = $gift_aid_settings item = giftAidSettings}
            <tr>
                <td>{$giftAidSettings.id}</td>
                <td>{$giftAidSettings.name}</td>
                <td>{$giftAidSettings.value}</td>
                <td>{$giftAidSettings.description}</td>
                <td>
                  <a href="{crmURL p='civicrm/gift-aid-submission-settings-Form' q="sid=`$giftAidSettings.id`&sname=`$giftAidSettings.name`" h=0}">[Edit]</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
        
    </table>
 
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</fieldset>
 
</div>

