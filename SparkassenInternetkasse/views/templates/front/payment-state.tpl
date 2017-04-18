
{capture name=path}
	<a href="order.php">
		{l s='Your shopping cart' mod='SparkassenInternetkasse'}
			</a><span class="navigation-pipe"> 
				{$navigationPipe|escape:'htmlall':'UTF-8'} </span> 
				{l s='SparkassenInternetkasseName' mod='SparkassenInternetkasse'}
	{/capture}
	
	{if $smarty.const._PS_VERSION_ < 1.6}
		{include file="$tpl_dir./breadcrumb.tpl"}
	{/if}
	
	<h1>{l s='Order summary' mod='SparkassenInternetkasse'}</h1>
{if $status == 'ok'}
		<p>
		<h3>{l s='Your order on %s is complete.' sprintf=$shop_name mod='SparkassenInternetkasse'}</h3>
		<br /><br />{l s='An email has been sent with this information.' mod='SparkassenInternetkasse'}
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='SparkassenInternetkasse'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='SparkassenInternetkasse'}</a>.
	</p>
{else if $status=='cancel'}
	<p class="warning">
		<h3>{l s='Your order on %s is canceld.' sprintf=$shop_name mod='SparkassenInternetkasse'}</h3>
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='SparkassenInternetkasse'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='SparkassenInternetkasse'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='SparkassenInternetkasse'}</a>.
	</p>
{/if}
<script>
if (window.frameElement) {
parent.location.href = self.location.href;
}
</script>