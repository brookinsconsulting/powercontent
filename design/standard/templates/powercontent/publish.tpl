{default $node=fetch('content','node',hash('node_id',ezini('NodeSettings','RootNode','content.ini')))
         $redirectToMainNode=false()}

<form method="post" action={"/powercontent/action"|ezurl} enctype="multipart/form-data">

<input type="hidden" name="NodeID" value="{$node.node_id}" />
<input type="hidden" name="UseNodeAssigments" value="0" />
<input type="hidden" name="ClassID" value="{$class.id}" />
{if $redirectToMainNode}
<input type="hidden" name="RedirectToMainNodeAfterPublish" />
{else}
<input type="hidden" name="RedirectURIAfterPublish" value="/{$node.url_alias}" />
{/if}

<input type="hidden" name="DoPublish" value="1" />

{foreach $class.data_map as $attribute}
<div class="block">
    <label>{$attribute.name|wash}</label>
    {powercontent_attribute_create_gui class_attribute=$attribute}
</div>
{/foreach}

<div class="buttonblock">
    <input class="button" type="submit" name="CreateButton" value="Create" />
</div>

</form>

{/default}