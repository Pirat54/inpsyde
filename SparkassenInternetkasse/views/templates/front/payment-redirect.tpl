	{capture name=path}<a href="order.php">{l s='Your shopping cart' mod='SparkassenInternetkasse'}</a><span class="navigation-pipe"> {$navigationPipe|escape:'htmlall':'UTF-8'} </span> {l s='SparkassenInternetkasseName' mod='SparkassenInternetkasse'}{/capture}
	{if $smarty.const._PS_VERSION_ < 1.6}
	{include file="$tpl_dir./breadcrumb.tpl"}
	{/if}
	<h1>{l s='Order summary' mod='SparkassenInternetkasse'}</h1>

	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{if $form_action_type=='iframe'}
	<iframe src="{$iframe_url}" scrolling="no" width="560px" height="770px" frameBorder="0">
	</iframe>
	{else}
	<form name="dpos" action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" data-ajax="false">
	
					{foreach key=mkey item=value from=$form_data}
						<input type="hidden" name="{$mkey}" value="{$value}" />
					{/foreach}
		
		
		<p class="cart_navigation">
			<input type="submit" name="redirect top payment gateway" value="{l s='Weiterleiten' mod='SparkassenInternetkasse'}" class="exclusive_large" />
		</p>
	</form>
	<script language="JavaScript">document.dpos.submit();</script>

	{/if}
	