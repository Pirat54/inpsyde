<div class="SparkassenInternetkasse-wrapper">
<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
	<fieldset>
		<legend>{l s='Basic configuration' mod='SparkassenInternetkasse'}</legend>

		

		<label for="SparkassenInternetkasse_sslmerchant">{l s='ssl merchant*:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_sslmerchant">
			<input type="text" name="SparkassenInternetkasse_sslmerchant" value="{$SPARKASSENINTERNETKASSE_SSLMERCHANT}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_secret">{l s='api key*:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_secret">
			<input type="text" name="SparkassenInternetkasse_secret" value="{$SPARKASSENINTERNETKASSE_SECRET}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_debug_mode">{l s='Debug mode :' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_debug_mode">
			<input type="radio" name="SparkassenInternetkasse_debug_mode" value ='OFF' {if $SPARKASSENINTERNETKASSE_DEBUG_MODE =='OFF'} checked="checked"{/if} />
				{l s='off' mod='SparkassenInternetkasse'}
			<input type="radio" name="SparkassenInternetkasse_debug_mode" value ='INFO' {if $SPARKASSENINTERNETKASSE_DEBUG_MODE =='INFO'} checked="checked"{/if} />
				{l s='Transaction' mod='SparkassenInternetkasse'}
			<input type="radio" name="SparkassenInternetkasse_debug_mode" value ='DEBUG' {if $SPARKASSENINTERNETKASSE_DEBUG_MODE =='DEBUG'} checked="checked"{/if} />
				{l s='Development' mod='SparkassenInternetkasse'}
		</div>
		<label for="SparkassenInternetkasse_log_file_path">{l s='Log file path:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_log_file_path">
			<input type="text" name="SparkassenInternetkasse_log_file_path" value="{$SPARKASSENINTERNETKASSE_LOG_FILE_PATH}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_test_mode">{l s='Test mode:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_test_mode">
			<input type="checkbox" name="SparkassenInternetkasse_test_mode" style="vertical-align: middle;" {if $SPARKASSENINTERNETKASSE_TEST_MODE}checked="checked"{/if} />
		</div>
		<label for="SparkassenInternetkasse_iframe_mode">{l s='Iframe mode:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_iframe_mode">
			<input type="checkbox" name="SparkassenInternetkasse_iframe_mode" style="vertical-align: middle;" {if $SPARKASSENINTERNETKASSE_IFRAME_MODE}checked="checked"{/if} />
		</div>
		<label for="SparkassenInternetkasse_payments">{l s='Paymentmethods* :' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cards">
			<input type="checkbox" name="SparkassenInternetkasse_cc" {if $SPARKASSENINTERNETKASSE_CC}checked="checked"{/if} />
				{l s='SparkassenInternetkasse Credit Card' mod='SparkassenInternetkasse'}<br>
			<input type="checkbox" name="SparkassenInternetkasse_dd" {if $SPARKASSENINTERNETKASSE_DD}checked="checked"{/if} />
				{l s='SparkassenInternetkasse Direct debit' mod='SparkassenInternetkasse'}<br>
			<input type="checkbox" name="SparkassenInternetkasse_gp" {if $SPARKASSENINTERNETKASSE_GP}checked="checked"{/if} />
				{l s='SparkassenInternetkasse Giropay' mod='SparkassenInternetkasse'}<br>
			<input type="checkbox" name="SparkassenInternetkasse_pp" {if $SPARKASSENINTERNETKASSE_PP}checked="checked"{/if} />
				{l s='SparkassenInternetkasse paypal' mod='SparkassenInternetkasse'}<br>
			<input type="checkbox" name="SparkassenInternetkasse_mp" {if $SPARKASSENINTERNETKASSE_MP}checked="checked"{/if} />
				{l s='SparkassenInternetkasse Masterpass' mod='SparkassenInternetkasse'}<br>
		</div>
		
		<label for="SparkassenInternetkasse_master_pass_user">{l s='Username Interface:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_master_pass_user">
			<input type="text" name="SparkassenInternetkasse_master_pass_user" value="{$SPARKASSENINTERNETKASSE_MASTER_PASS_USER}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_master_pass_secret">{l s='Password Interface:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_master_pass_secret">
			<input type="text" name="SparkassenInternetkasse_master_pass_secret" value="{$SPARKASSENINTERNETKASSE_MASTER_PASS_SECRET}" style="vertical-align: middle;" />
		</div>

		<label for="SparkassenInternetkasse_hold_review_os">{l s='Order status:  "Hold for Review" ' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form">
			<select id="SparkassenInternetkasse_hold_review_os" name="SparkassenInternetkasse_hold_review_os">';
				// Hold for Review order state selection
				{foreach from=$order_states item='os'}
					<option value="{$os.id_order_state|intval}" {if ((int)$os.id_order_state == $SPARKASSENINTERNETKASSE_HOLD_REVIEW_OS)} selected{/if}>
						{$os.name|stripslashes}
					</option>
				{/foreach}
			</select>
		</div>
		<br />
		<center>
			<input type="submit" name="submitModule" value="{l s='Update settings' mod='SparkassenInternetkasse'}" class="button" />
		</center>
		<sub>{l s='* mandatory' mod='SparkassenInternetkasse'}</sub>
	</fieldset>
	<!------------ CC ------------>
	<fieldset>
		<legend>{l s='Credit Card configuration' mod='SparkassenInternetkasse'}</legend>
		
		<label for="SparkassenInternetkasse_cc_paymentoptions">{l s='Paymentoptions:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cc_paymentoptions">
			<input type="text" name="SparkassenInternetkasse_cc_paymentoptions" value="{$SPARKASSENINTERNETKASSE_CC_PAYMENTOPTIONS}" style="vertical-align: middle;" />
		</div>
		
		<label for="SparkassenInternetkasse_cc_autocapture">{l s='Capture after:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cc_autocapture">
			<input type="text" name="SparkassenInternetkasse_cc_autocapture" value="{$SPARKASSENINTERNETKASSE_CC_AUTOCAPTURE}" style="vertical-align: middle;" />
		</div>
		
		<label for="SparkassenInternetkasse_cc_acceptcountries">{l s='Acceptcountries:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cc_acceptcountries">
			<input type="text" name="SparkassenInternetkasse_cc_acceptcountries" value="{$SPARKASSENINTERNETKASSE_CC_ACCEPTCOUNTRIES}" style="vertical-align: middle;" />
		</div>
		
		<label for="SparkassenInternetkasse_cc_deliverycountry_action">{l s='Deliverycountry action:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cc_deliverycountry_action">
			<input type="radio" name="SparkassenInternetkasse_cc_deliverycountry_action" value ='notify' {if $SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION=='notify'}checked="checked"{/if} />
				{l s='Notify' mod='SparkassenInternetkasse'}
			<input type="radio" name="SparkassenInternetkasse_cc_deliverycountry_action" value ='reject' {if $SPARKASSENINTERNETKASSE_CC_DELIVERYCOUNTRY_ACTION=='reject'}checked="checked"{/if} />
				{l s='Reject' mod='SparkassenInternetkasse'}		
		</div>

		<label for="SparkassenInternetkasse_cc_rejectcountries">{l s='Rejectcountries:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_cc_rejectcountries">
			<input type="text" name="SparkassenInternetkasse_cc_rejectcountries" value="{$SPARKASSENINTERNETKASSE_CC_REJECTCOUNTRIES}" style="vertical-align: middle;" />
		<br>{l s='list of card issuing countries that will be rejected. Two letter country codes according to ISO 3166. If not provided, all issuing countries will be accepted' mod='SparkassenInternetkasse'}
		</div>
		

		
	
	</fieldset>
	<!------------ DD ------------>
	
	<fieldset>
		<legend>{l s='Direct debit configuration' mod='SparkassenInternetkasse'}</legend>
		
		<label for="SparkassenInternetkasse_dd_paymentoptions">{l s='Paymentoptions:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_dd_paymentoptions">
			<input type="text" name="SparkassenInternetkasse_dd_paymentoptions" value="{$SPARKASSENINTERNETKASSE_DD_PAYMENTOPTIONS}" style="vertical-align: middle;" />
		</div>
		
		<label for="SparkassenInternetkasse_dd_autocapture">{l s='Capture after:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_dd_autocapture">
			<input type="text" name="SparkassenInternetkasse_dd_autocapture" value="{$SPARKASSENINTERNETKASSE_DD_AUTOCAPTURE}" style="vertical-align: middle;" />
			<br>{l s='period - in hours - between reservation and automatic capture. Value may range from  0-720:' mod='SparkassenInternetkasse'}
		</div>	
		
		<label for="SparkassenInternetkasse_dd_mandatename">{l s='Mandate name:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_dd_mandatename">
			<input type="text" name="SparkassenInternetkasse_dd_mandatename" value="{$SPARKASSENINTERNETKASSE_DD_MANDATENAME}" style="vertical-align: middle;" />
		</div>
		
		<label for="SparkassenInternetkasse_dd_mandateprefix">{l s='Mandate reference prefix:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_dd_mandateprefix">
			<input type="text" name="SparkassenInternetkasse_dd_mandateprefix" value="{$SPARKASSENINTERNETKASSE_DD_MANDATEPREFIX}" style="vertical-align: middle;" />
		</div>
		
		
		<label for="SparkassenInternetkasse_dd_sequencetype">{l s='Sequencetype :' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_dd_sequencetype">
			<input type="radio" name="SparkassenInternetkasse_dd_sequencetype" value ='oneoff' {if $SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE=='oneoff'} checked="checked"{/if} />
				{l s='oneoff' mod='SparkassenInternetkasse'}
			<input type="radio" name="SparkassenInternetkasse_dd_sequencetype" value ='first' {if $SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE=='first'}checked="checked"{/if} />
				{l s='first' mod='SparkassenInternetkasse'}
			<input type="radio" name="SparkassenInternetkasse_dd_sequencetype" value ='recurring' {if $SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE=='recurring'}checked="checked"{/if} />
				{l s='recurring' mod='SparkassenInternetkasse'}			
			<input type="radio" name="SparkassenInternetkasse_dd_sequencetype" value ='final'  {if $SPARKASSENINTERNETKASSE_DD_SEQUENCETYPE=='final'}checked="checked"{/if} />
				{l s='final' mod='SparkassenInternetkasse'}	</div>
	</fieldset>
	<!------------ GP ------------>
	<fieldset>
	
		<legend>{l s='Giropay configuration' mod='SparkassenInternetkasse'} </legend>
	
		<label for="SparkassenInternetkasse_transactiontype">{l s='Transactiontype :' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_transactiontype">
			<input type="radio" name="SparkassenInternetkasse_gp_transactiontype" value ='authorization' {if $SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE=='authorization'}checked="checked"{/if} />
				authorization
			<input type="radio" name="SparkassenInternetkasse_gp_transactiontype" value ='preauthorization' {if $SPARKASSENINTERNETKASSE_GP_TRANSACTIONTYPE=='preauthorization'}checked="checked"{/if} />
				preauthorization
		</div>
		
		<label for="SparkassenInternetkasse_age_verification">{l s='Age verification:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_age_verification">
			<input type="checkbox" name="SparkassenInternetkasse_age_verification" style="vertical-align: middle;" {if $SPARKASSENINTERNETKASSE_AGE_VERIFICATION}checked="checked"{/if} />
		</div>
		
		<label for="SparkassenInternetkasse_label0">{l s='Label 1:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_label0">
			<input type="text" name="SparkassenInternetkasse_label0" value="{$SPARKASSENINTERNETKASSE_LABEL0}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_text0">{l s='Text 1:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_text0">
			<input type="text" name="SparkassenInternetkasse_text0" value="{$SPARKASSENINTERNETKASSE_TEXT0}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_label1">{l s='Label 2:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_label1">
			<input type="text" name="SparkassenInternetkasse_label1" value="{$SPARKASSENINTERNETKASSE_LABEL1}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_text1">{l s='Text 2:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_text1">
			<input type="text" name="SparkassenInternetkasse_text1" value="{$SPARKASSENINTERNETKASSE_TEXT1}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_label2">{l s='Label 3:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_LABEL2">
			<input type="text" name="SparkassenInternetkasse_label2" value="{$SPARKASSENINTERNETKASSE_LABEL2}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_text2">{l s='Text 3:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_text2">
			<input type="text" name="SparkassenInternetkasse_text2" value="{$SPARKASSENINTERNETKASSE_TEXT2}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_label3">{l s='Label 4:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_LABEL3">
			<input type="text" name="SparkassenInternetkasse_label3" value="{$SPARKASSENINTERNETKASSE_LABEL3}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_text3">{l s='Text 4:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_text3">
			<input type="text" name="SparkassenInternetkasse_text3" value="{$SPARKASSENINTERNETKASSE_TEXT3}" style="vertical-align: middle;" />
		</div>
				<label for="SparkassenInternetkasse_label4">{l s='Label 5:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_LABEL4">
			<input type="text" name="SparkassenInternetkasse_label4" value="{$SPARKASSENINTERNETKASSE_LABEL4}" style="vertical-align: middle;" />
		</div>
		<label for="SparkassenInternetkasse_text4">{l s='Text 5:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_text4">
			<input type="text" name="SparkassenInternetkasse_text4" value="{$SPARKASSENINTERNETKASSE_TEXT4}" style="vertical-align: middle;" />
		</div>
	</fieldset>
	
	<!------------ MP ------------>
	<fieldset>
		<legend>{l s='Masterpass configuration' mod='SparkassenInternetkasse'} </legend>
		<label for="SparkassenInternetkasse_address_from_masterpass">{l s='Address from Masterpass:' mod='SparkassenInternetkasse'}</label>
		<div class="margin-form" id="SparkassenInternetkasse_address_from_masterpass">
			<input type="checkbox" name="SparkassenInternetkasse_address_from_masterpass" style="vertical-align: middle;" {if $SPARKASSENINTERNETKASSE_MASTER_PASS_ADDRESS}checked="checked"{/if} />
		</div>
	</fieldset>
</form>
</div>
