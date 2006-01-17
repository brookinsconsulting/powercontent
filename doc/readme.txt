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


 Installation instructions
****************************

1. Enable the extension (in site.ini.append or by using the admin interface)

2. Add a policy for this module to the desired roles.


 Example code
****************************

A typical naming scheme for the input fields of an attribute is:

ContentObjectAttribute_[some attribute specific name]_[contentobjectattribute id]

You will have to to change these names to:

powercontent_[contentclassattribute identifier]_ContentObjectAttribute_[some attribute specific name]_pcattributeid

When the Power Content module processes this kind of post variables, it will inject fake post variables where the string "pcattributeid" will be replaced by the contentobjectattribute id corresponding to the contentclassattribute identifier specified in the post variable name.

Example code for the "File" content class:

<form method="post" action={"powercontent/action"|ezurl} enctype="multipart/form-data">
    <div>
    <input type="hidden" name="NodeID" value="{$node.main_node_id}" />
    <label>Publish immediately:</label> <input type="checkbox" name="DoPublish" checked="checked" />
    <input type="hidden" name="UseNodeAssigments" value="0" />
    <input type="hidden" name="ClassID" value="12" />
    <input type="hidden" name="RedirectURIAfterPublish" value={$node.url_alias|ezurl} />
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