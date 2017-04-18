	{capture name=path}<a href="order.php">{l s='Your shopping cart' mod='SparkassenInternetkasse'}</a><span class="navigation-pipe"> {$navigationPipe|escape:'htmlall':'UTF-8'} </span> {l s='SparkassenInternetkasseName' mod='SparkassenInternetkasse'}{/capture}
	{if $smarty.const._PS_VERSION_ < 1.6}
	{include file="$tpl_dir./breadcrumb.tpl"}
	{/if}
	<h1>{l s='Pay with Masterpass' mod='SparkassenInternetkasse'}</h1>


	<div>{$string}</div>

	