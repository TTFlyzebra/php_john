<?php

/**
 * 重组节点信息
 * @param unknown $node
 * @param number $pid
 */
function node_merge($node, $access = null, $pid = 0) {
	$arr = array ();
	foreach ( $node as $v ) {
		if ($v ['pid'] == $pid) {
			if (is_array ( $access )) {
				$v ['access'] = in_array ( $v ['id'], $access ) ? 1 : 0;
			}
			
			$v ['child'] = node_merge ( $node, $access, $v ['id'] );
			$arr [] = $v;
		}
	}
	return $arr;
}

function path_merge($data) {
	$arr = array ();
	foreach ( $data as $v ) {
		$arr [] = get_path ( $data, $v ['id'] );
	}
	return $arr;
}

/**
 * 获取RBAC所有能访问的节点的路径
 * @param unknown $data
 * @param unknown $id
 * @return string
 */
function get_path($data, $id) {
	foreach ( $data as $v ) {
		if ($v ['id'] != $id)
			continue;
		if ($v ['pid'] == 0) {
			$path = '/' . $v ['name'];
		} else {
			$path = get_path ( $data, $v ['pid'] ) . '/' . $v ['name'];
		}
	}
	return $path;
}