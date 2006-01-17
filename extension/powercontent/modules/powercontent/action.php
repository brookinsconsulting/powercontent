<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
$http =& eZHTTPTool::instance();

$module =& $Params['Module'];

// The button was called NewButton first, but because 
// there was a module action triggered in content/relation_edit.php because of that button name
// I've renamed it to 'CreateButton'
if ( $http->hasPostVariable( 'CreateButton' ) && $http->hasPostVariable( 'NodeID' )  )
{
    $node =& eZContentObjectTreeNode::fetch( $http->postVariable( 'NodeID' ) );
    
    if ( is_object( $node ) )
    {
        $contentClassID = false;
        $contentClassIdentifier = false;
        $class = false;
        
        if ( $http->hasPostVariable( 'ClassID' ) )
        {
            $contentClassID = $http->postVariable( 'ClassID' );
            $class =& eZContentClass::fetch( $contentClassID );
        }
        else if ( $http->hasPostVariable( 'ClassIdentifier' ) )
        {
            $contentClassIdentifier = $http->postVariable( 'ClassIdentifier' );
            $class =& eZContentClass::fetchByIdentifier( $contentClassIdentifier );
        }
        
        if ( is_object( $class ) )
        {
            $contentClassID = $class->attribute( 'id' );

            $parentContentObject =& $node->attribute( 'object' );
            if ( $parentContentObject->checkAccess( 'create', $contentClassID,  $parentContentObject->attribute( 'contentclass_id' ) ) == '1' )
            {
                $user =& eZUser::currentUser();
                $userID =& $user->attribute( 'contentobject_id' );
                $sectionID = $parentContentObject->attribute( 'section_id' );

                $db =& eZDB::instance();
                $db->begin();
                
                $object =& $class->instantiate( $userID, $sectionID );
                $ObjectID = $object->attribute( 'id' );
                
                $version =& $object->currentVersion();
                $EditVersion = $version->attribute( 'version' );
                
                $EditLanguage = false;

                $time = mktime();
                
                $version->setAttribute( 'created', $time );
                $version->setAttribute( 'modified', $time );

                $object->setAttribute( 'modified', $time );
                
                $dataMap =& $version->dataMap();
                
                $nodeAssignment =& eZNodeAssignment::create( array(
                                                                     'contentobject_id' => $object->attribute( 'id' ),
                                                                     'contentobject_version' => $object->attribute( 'current_version' ),
                                                                     'parent_node' => $node->attribute( 'node_id' ),
                                                                     'is_main' => 1
                                                                   )
                                                            );
                                            
                if ( $http->hasPostVariable( 'AssignmentRemoteID' ) )
                {
                    $nodeAssignment->setAttribute( 'remote_id', $http->postVariable( 'AssignmentRemoteID' ) );
                }
                
                $nodeAssignment->store();
                
                /*
                    handle attribute values
                    
                    conversion scheme:
                    
                    powercontent_[attributeidentifier]_[normalpostvariablename]
                    where the attribute id in normalpostvariablename has been replaced with 'pcattributeid'
                */
                
                $postVariables = $_POST;
                
                $usedAttributes = array();
                
                foreach ( $postVariables as $postName => $postValue )
                {
                       $newPostVariable = false;
                       $nameParts = explode( '_', $postName );
                       
                       if ( count( $nameParts ) > 2 )
                       {
                           $firstNamePart = array_shift( $nameParts );
                           eZDebug::writeNotice( $firstNamePart );
                           
                           if ( $firstNamePart != 'powercontent' )
                           {
                               continue;
                           }
                           
                           $possibleAttributeIdentifier = '';
                           
                           while ( true )
                           {
                               $part = array_shift( $nameParts );
                               
                               if ( is_null( $part ) )
                               {
                                   break;
                               }
                               
                               $possibleAttributeIdentifier = $possibleAttributeIdentifier . $part;
                           
                               if ( array_key_exists( $possibleAttributeIdentifier, $dataMap ) )
                               {
                                   eZDebug::writeNotice( 'found matching attribute: ' . $possibleAttributeIdentifier, 'defaultvalues/action.php' );
                                   $usedAttributes[] =& $dataMap[$possibleAttributeIdentifier];
                                   $attribID = $dataMap[$possibleAttributeIdentifier]->attribute( 'id' );
                                   $newPostVariable = implode( '_', $nameParts );
                                   $newPostVariable = str_replace( 'pcattributeid', $attribID, $newPostVariable );
                                   $http->setPostVariable( $newPostVariable, $postValue );
                                   break;
                               }
                            }
                       }
                }
                
                $fileVariables = &$_FILES;
                
                foreach ( $fileVariables as $fileName => $fileValue )
                {
                       $newFileVariable = false;
                       $nameParts = explode( '_', $fileName );
                       
                       if ( count( $nameParts ) > 2 )
                       {
                           $firstNamePart = array_shift( $nameParts );
                           eZDebug::writeNotice( $firstNamePart );
                           
                           if ( $firstNamePart != 'powercontent' )
                           {
                               continue;
                           }
                           
                           $possibleAttributeIdentifier = '';
                           
                           while ( true )
                           {
                               $part = array_shift( $nameParts );
                               
                               if ( is_null( $part ) )
                               {
                                   break;
                               }
                               
                               $possibleAttributeIdentifier = $possibleAttributeIdentifier . $part;
                           
                               if ( array_key_exists( $possibleAttributeIdentifier, $dataMap ) )
                               {
                                   eZDebug::writeNotice( 'found matching file attribute: ' . $possibleAttributeIdentifier, 'defaultvalues/action.php' );
                                   $usedAttributes[] =& $dataMap[$possibleAttributeIdentifier];
                                   $attribID = $dataMap[$possibleAttributeIdentifier]->attribute( 'id' );
                                   $newFileVariable = implode( '_', $nameParts );
                                   $newFileVariable = str_replace( 'pcattributeid', $attribID, $newFileVariable );
                                   $_FILES[$newFileVariable] = $fileValue;
                                   break;
                               }
                            }
                       }
                }
                
                if ( count( $usedAttributes ) > 0 )
                {
                    if ( $http->hasPostVariable( 'DoPublish' ) )
                    {
                        $http->setPostVariable( 'PublishButton', 'Publish' );
                    } 
                    else
                    {
                        $http->setPostVariable( 'StoreButton', 'Store' );
                    }
                }
                
                $db->commit();
                
                // let edit module run in the same HTTP request (no redirect!!)
                // I don't think this has any consequences for custom actions
                // which will use the content browser etc.
                $Result = array();
                $Result['rerun_uri'] = $module->redirectionURI( 'content', 'edit', array( $ObjectID, $EditVersion, $EditLanguage ) );
                $module->setExitStatus( EZ_MODULE_STATUS_RERUN );
                return;
            }
            else
            {
                return $module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
            }
        }
        else
        {
            return $module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
        }
    }
    else
    {
        return $module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
    }
}
    
return $module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
?>