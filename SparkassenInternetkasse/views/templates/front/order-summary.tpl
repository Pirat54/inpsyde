	{capture name=path}<a href="order.php">{l s='Your shopping cart' mod='SparkassenInternetkasse'}</a><span class="navigation-pipe"> {$navigationPipe|escape:'htmlall':'UTF-8'} </span> {l s='SparkassenInternetkasseName' mod='SparkassenInternetkasse'}{/capture}
	{if $smarty.const._PS_VERSION_ < 1.6}
	{include file="$tpl_dir./breadcrumb.tpl"}
	{/if}
	<h1>{l s='Order summary' mod='SparkassenInternetkasse'}</h1>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	<form id="dpos" action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" data-ajax="false">
	
					{foreach key=mkey item=value from=$form_data}
						<input type="hidden" name="{$mkey}" value="{$value}" />
					{/foreach}
		
		
		<p class="cart_navigation">
			<input type="submit" name="confirmation" value="{l s='Weiterleiten' mod='SparkassenInternetkasse'}" class="exclusive_large" />
		</p>
	</form>
	<script language="JavaScript">document.dpos.submit();</script>

