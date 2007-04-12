<?php

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'function' => 'eZPowercontentForwardInit',
                                    'function_names' => array( 'powercontent_create_gui',
                                                               'powercontent_attribute_create_gui' ) );

if ( !function_exists( 'eZPowercontentForwardInit' ) )
{
    function eZPowercontentForwardInit()
    {
        /*
            keys you can use:
            - template_root: folder from where the templates will be loaded
            - input_name: name of the input variable on which the function operates
            - output_name: under which name the variable is available in the loaded template
            - namespace: namespace of the template
            - attribute_keys: attributes of the inputted variable that will serve as design keys
            - attribute_access: which attributes have to be used to get the location of the template to fetch
                these parameters can be both an array of attributes
                    e.g. array( array( 'data_type', 'information', 'string' ) )
                    means:
                    $class_attribute.data_type.information.string
            - use_views: false if not used, a string to give the name of the template to use
        */

        include_once( 'kernel/common/ezobjectforwarder.php' );
        $forward_rules = array(
            'powercontent_create_gui' => array( 'template_root' => 'powercontent',
                                           'input_name' => 'content_class',
                                           'output_name' => 'class',
                                           'namespace' => 'powercontent',
                                           'attribute_keys' => array( 'class_identifier' => array( 'identifier' ),
                                                                      'class' => array( 'id' ) ),
                                           'attribute_access' => array(),
                                           'use_views' => 'view' ),
            'powercontent_attribute_create_gui' => array( 'template_root' => 'powercontent/datatype',
                                           'input_name' => 'class_attribute',
                                           'output_name' => 'class_attribute',
                                           'namespace' => 'powercontent_attribute',
                                           'attribute_keys' => array( 'attribute_identifier' => array( 'identifier' ),
                                                                      'class' => array( 'contentclass_id' ) ),
                                           // todo: add class_identifier key, a patch for eZContentClassAttribute is needed
                                           'attribute_access' => array( array( 'data_type',
                                                                               'information',
                                                                               'string' ) ),
                                           'use_views' => false ) );

        return new eZObjectForwarder( $forward_rules );
    }
}

?>