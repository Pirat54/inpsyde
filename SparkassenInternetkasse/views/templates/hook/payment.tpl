{if $SPARKASSENINTERNETKASSE_CC === 'on'}
<div class="row">
    <div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a href="{$link->getModuleLink('SparkassenInternetkasse', 'redirect', ['paymentmethod'=>'CC'])|escape:'html':'UTF-8'}" title="{l s='SparkassenInternetkasseName Creditcard' mod='SparkassenInternetkasse'}" class="cc">
            <span class="linktext">{l s='SparkassenInternetkasse Creditcard' mod='SparkassenInternetkasse'}</span>
        </a>
    </p>
    </div>
</div>
{/if}
{if $SPARKASSENINTERNETKASSE_DD === 'on'}
<div class="row">
    <div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a href="{$link->getModuleLink('SparkassenInternetkasse', 'redirect', ['paymentmethod'=>'DD'])|escape:'html':'UTF-8'}" title="{l s='SparkassenInternetkasseName Directdebit' mod='SparkassenInternetkasse'}" class="cc">
            <span class="linktext">{l s='SparkassenInternetkasse Directdebit' mod='SparkassenInternetkasse'}</span>
        </a>
    </p>
    </div>
</div>
{/if}
{if $SPARKASSENINTERNETKASSE_GP === 'on'}
<div class="row">
    <div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a href="{$link->getModuleLink('SparkassenInternetkasse', 'redirect', ['paymentmethod'=>'GP'])|escape:'html':'UTF-8'}" title="{l s='SparkassenInternetkasseName Giropay' mod='SparkassenInternetkasse'}" class="cc">
            <span class="linktext">{l s='SparkassenInternetkasse Giropay' mod='SparkassenInternetkasse'}</span>
        </a>
    </p>
    </div>
</div>
{/if}
{if $SPARKASSENINTERNETKASSE_PP === 'on'}
<div class="row">
    <div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a href="{$link->getModuleLink('SparkassenInternetkasse', 'redirect', ['paymentmethod'=>'PP'])|escape:'html':'UTF-8'}" title="{l s='SparkassenInternetkasseName Paypal' mod='SparkassenInternetkasse'}" class="cc">
            <span class="linktext">{l s='SparkassenInternetkasse Paypal' mod='SparkassenInternetkasse'}</span>
        </a>
    </p>
    </div>
</div>
{/if}
{if $SPARKASSENINTERNETKASSE_MP === 'on'}
<div class="row">
    <div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a href="{$link->getModuleLink('SparkassenInternetkasse', 'redirect', ['paymentmethod'=>'MP'])|escape:'html':'UTF-8'}" title="{l s='SparkassenInternetkasseName Masterpass' mod='SparkassenInternetkasse'}" class="cc">
            <span class="linktext">{l s='SparkassenInternetkasse Masterpass' mod='SparkassenInternetkasse'}</span>
        </a>
    </p>
    </div>
</div>
{/if}