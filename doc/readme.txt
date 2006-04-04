Power Content extension

Copyright (C) 2006 SCK-CEN
Written by Kristof Coomans ( kristof[dot]coomans[at]telenet[dot]be )

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

 Features
****************************

The Power content module allows one-click publishing or pre-filling of attributes, similar to the Object Creator extension (http://ez.no/community/contribs/hacks/object_creator). However, it uses content/edit for the final processing.

This module has one big advantage: it can deal with any type of attribute, even new custom ones.

 Installation instructions
****************************

1. Enable the extension (in site.ini.append or by using the admin interface)

2. Add a policy for this module to the desired roles.


How to use in your template ?
****************************

The main idea is to add new attributes that are going to contain the values you want instead of the default ones on ez publish. This module associate these new fields with standard fields that exist when editing with the default edit template. Therefore, you have to understand how ez names these fields so you can add the values you want.

A typical naming scheme for the input fields of an attribute is:

ContentObjectAttribute_[some attribute specific name]_[contentobjectattribute id]

For instance the name for the fields of an articles are (do a view source of the page when you're editing an article):
- title: ContentObjectAttribute_ezstring_data_text_1105
- into: ContentObjectAttribute_data_text_1108
(1105 and 1108 are contentobjectattribute identifier numbers that are going to be different on your installation.)

You will have to to change these names to:

powercontent_[contentclassattribute identifier]_ContentObjectAttribute_[some attribute specific name]_pcattributeid

For the article, it would be:
-title: powercontent_title_ContentObjectAttribute_ezstring_data_text_pcattributeid
-intro: powercontent_intro_ContentObjectAttribute_data_text_pcattributeid
 
When the Power Content module processes this kind of post variables, it will inject fake post variables where the string "pcattributeid" will be replaced by the contentobjectattribute id corresponding to the contentclassattribute identifier specified in the post variable name.

On the top of the parameters you want to "prefill", you have to provide a few additionnal ones:
- NodeID: The nodeid of the parent object you want to create
- ClassIdentifier: The class identifier of the object you want to create
(you can provide either the class identifier or the class id, no need for both) 
- CreateButton: a submit button
You have more optional parameters, see the examples 

 Examples code
****************************

1) Example code for the "article" content class. This will prefill the title and intro of the article:
<form method="post" action={"powercontent/action/"|ezurl}>
  <div class="buttonright">
  <input type="hidden" name="NodeID" value="{$owner.contentobject.main_node_id}" />
  <input type="hidden" name="ClassID" value="2" />
  <input type="hidden" name="powercontent_title_ContentObjectAttribute_ezstring_data_text_pcattributeid" value="A new article on my blog" />
  <input type="hidden" name="powercontent_intro_ContentObjectAttribute_data_text_pcattributeid" value="A new intro on my blog" />
   <input class="classbutton" type="submit" name="CreateButton" value="New article" />
</div>
</form>

2) Example code for the "File" content class:

<form method="post" action={"powercontent/action"|ezurl} enctype="multipart/form-data">
    <div>
    <input type="hidden" name="NodeID" value="{$node.main_node_id}" />
    <label>Publish immediately:</label> <input type="checkbox" name="DoPublish" checked="checked" />
    <input type="hidden" name="UseNodeAssigments" value="0" />
    <input type="hidden" name="ClassID" value="12" />
    <input type="hidden" name="RedirectURIAfterPublish" value="/{$node.url_alias}" />
    </div>
    
    <div>
    <label>File:</label>
    <input class="box" name="powercontent_file_ContentObjectAttribute_data_binaryfilename_pcattributeid" type="file" />
    </div>
    <div>
    <label>Title:</label>
    <input type="text" name="powercontent_name_ContentObjectAttribute_ezstring_data_text_pcattributeid" value="" />
    </div>
    <div>
    <input class="button" type="submit" name="CreateButton" value="Create" />
    </div>
</form>
