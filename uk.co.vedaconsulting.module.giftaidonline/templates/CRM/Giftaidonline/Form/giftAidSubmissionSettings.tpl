<div class="crm-block crm-form-block crm-page-form-block">
  
<fieldset>
    <table>
        <tr>
            <td>
                Id :
            </td>
            <td>
                {$settings_id}
                <input type="text" name="id" value={$settings_id} id="setting_id" /><br />
            </td>
        </tr>
        <tr>
            <td>
                Name :
            </td>
            <td>
                {$settings_name}
               <input type="text" name="name" value={$settings_name} id="setting_name" /><br />
            </td>
        </tr>
        <tr>
             <td>
               Value :
            </td>
            <td>
                <input type="text" name="value" value="" />
            </td>
        </tr>
    </table>
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</fieldset>
 
</div>

{literal}
  <script type="text/javascript">
  cj( document ).ready( function ( ) {
    cj("#setting_name").hide();
    cj("#setting_id").hide();
  });
</script>
{/literal}
