
{if $AUTH_SUCCESSFULL ne 'true'}
    <form id="ConfigureGoogleSettings" name="ConfigureGoogleSettings" enctype='multipart/form-data' method="POST"
          action="index.php?module=Administration&action=googleAccountSettings&athenticate=1&save=1">

        <span class='error'>{$error.main}</span>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer">
            <tr>
                <td>
                    {$BUTTONS}
                </td>
            </tr>
        </table>

        <table width="10%" border="0" cellspacing="1" cellpadding="0" class="edit view"  style="border-collapse:separate;border-spacing:1em;margin:15px 0px;">

            <tr><th align="left" scope="row" colspan="4"><h4>{$MOD.LBL_GOOGLE_ACCOUNT_SETTINGS_TITLE}</h4></th>
            </tr>
            <tr>
                <td width="10%"><label>{$MOD.LBL_GD_GMAIL_ID}</label></td>
                <td><input type="text" id="USER_EMAIL" name="USER_EMAIL" placeholder="{$MOD.LBL_GD_GMAIL_ID}" value="{$USER_EMAIL}" style="width:60%"/></td>
            </tr>
            <tr>
                <td width="10%"><label>{$MOD.LBL_PARENT_DIRECTORY}</label></td>
                <td><input type="text" id="PARENT_DIR" name="PARENT_DIR" placeholder="{$MOD.LBL_PARENT_DIRECTORY}" value="{$PARENT_DIR}" style="width:60%"/></td>
            </tr>
            <tr>
                <td width="10%"><label>{$MOD.LBL_GD_CLIENT_ID}</label></td>
                <td><input type="text" id="CLIENT_ID" name="CLIENT_ID" placeholder="{$MOD.LBL_GD_CLIENT_ID}" value="{$CLIENT_ID}" style="width:60%"/></td>
            </tr>
            <tr>
                <td width="10%"><label>{$MOD.LBL_GD_CLIENT_SECRET}</label></td>
                <td><input type="text" id="CLIENT_SECRET" name="CLIENT_SECRET" placeholder="{$MOD.LBL_GD_CLIENT_SECRET}" value="{$CLIENT_SECRET}" style="width:60%"/></td>
            </tr>
            <tr>
                <td width="10%"><label>{$MOD.LBL_SCOPE}</label></td>
                <td><input type="text" id="SCOPES" name="SCOPES" placeholder="{$MOD.LBL_SCOPE}" value="{$SCOPES}" style="width:60%"/></td>
            </tr>
            <!--<tr>
                <td width="10%"><label>{$MOD.LBL_GD_STATE}</label></td>
                <td><input type="text" id="STATE" name="STATE" placeholder="{$MOD.LBL_GD_STATE}" value="{$STATE}" style="width:60%"/></td>
            </tr> -->
            <tr>
                <td width="10%"><label>{$MOD.LBL_GD_REDIRECT_URI}</label></td>
                <td><input type="text" id="REDIRECT_URI" name="REDIRECT_URI" placeholder="{$MOD.LBL_GD_REDIRECT_URI}" value="{$REDIRECT_URI}" style="width:60%"/></td>
            </tr>
        </table>
        <div style="padding-top: 2px;">
            {$BUTTONS}
        </div>
        {$JAVASCRIPT}
    </form>
{/if}

