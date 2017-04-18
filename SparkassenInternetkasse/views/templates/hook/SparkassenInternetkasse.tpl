<p class="payment_module" >	
{if $isFailed == 1}		
<p style="color: red;">			
	{if !empty($smarty.get.message)}
		{l s='Error detail from SparkassenInternetkasseName : ' mod='SparkassenInternetkasseName'}
		{$smarty.get.message|htmlentities}
	{else}	
		{l s='Error, please verify the card information' mod='SparkassenInternetkasseName'}
	{/if}
</p>
{/if}
	<form name="SparkassenInternetkasseName_form" id="SparkassenInternetkasseName_form" action="{$module_dir}validation.php" method="post">
		<span style="border: 1px solid #595A5E;display: block;padding: 0.6em;text-decoration: none;margin-left: 0.7em;">
			
					
		<div id="aut2">				
		<br /><br />				
			
		<input type="hidden" name="x_solution_ID" value="A1000006" />				
		<input type="hidden" name="x_invoice_num" value="{$x_invoice_num|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="x_currency_code" value="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
		<label style="margin-top: 4px; margin-left: 35px;display: block;width: 90px;float: left;">{l s='Full name' mod='SparkassenInternetkasse'}</label> 
		<label style="margin-top: 4px; margin-left: 35px; display: block;width: 90px;float: left;">{l s='SparkassenInternetkasse Name ' mod='SparkassenInternetkasse'}</label>
		{if $payments.CC == 1}
			<input type="radio" name="SparkassenInternetkasse_payment_prefix" value ='CC'  />Credit Card <br>
		{/if}
		{if $payments.DD == 1}
			<input type="radio" name="SparkassenInternetkasse_payment_prefix" value ='DD'  />Direct debit<br>
		{/if}
		{if $payments.GP == 1}
			<input type="radio" name="SparkassenInternetkasse_payment_prefix" value ='GP'  />Giropay<br>
		{/if}
		{if $payments.PP == 1}
			<input type="radio" name="SparkassenInternetkasse_payment_prefix" value ='PP'  />Pay Pal<br>
		{/if}
		{if $payments.MP == 1}
			<input type="radio" name="SparkassenInternetkasse_payment_prefix" value ='MP'  />Masterpass<br>
		{/if}
	<input type="button" id="asubmit" value="{l s='Validate order' mod='SparkassenInternetkasseName'}" style="margin-left: 124px; padding-left: 25px; padding-right: 25px;" class="button" />
</div>		
</span>	
</form>

