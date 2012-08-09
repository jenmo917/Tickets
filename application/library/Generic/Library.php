<?php
class Generic_Library
{
	public function changeKeys($originalArray = array(), $keyMapping = array())
   	{
   		// Extract keys
    	$originalKeys = array_keys($originalArray);
    	$originalKeysFliped = array_flip($originalKeys);
    	$originalValues = array_values($originalArray);
    	$wantedKeys = array_values($keyMapping);
    	// Get the indexes of the keys that are going to be changed.
    	$intersectKeysIndexes = array_values(array_intersect_key($originalKeysFliped, $keyMapping));
    	// Make sure that the right keys are changed by keeping the indexes.
    	$keyMapping = array_combine($intersectKeysIndexes, $wantedKeys);
		$newKeys = array_replace($originalKeys, $keyMapping);
		// Restore associative array.
		$keyMappedArray = array_combine($newKeys, $originalValues);
		return $keyMappedArray;
    	}

}