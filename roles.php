<?php

namespace {{NameSpace}}\roles;

defined( 'ABSPATH' ) or exit;

/**
 * Handles registering and removing roles and capabilities of
 * {{Plugin Name}}.
 * 
 * replace with filecopy
 */

class {{PluginClass}}_Roles
{
	protected string $domain = {{PLUGIN_PREFIX}}_TEXT_DOMAIN;

	/**
	 * Roles to add and remove.
	 */
	protected array $roles = array(
		'role_snake' => 'Role Name',
	);

	/**
	 * Role capabilities to add or modify.
	 */
	protected array $capabilities = array(
		array(
			'roles' => 'administrator',
			'capabilities' => 'admin_task',
		),
		array(
			'roles' => array(
				'administrator',
				'role_snake',
			),
			'capabilities' => array(
				'task_one',
				'task_two',
			),
		),
	);

	public function __construct()
	{
		register_activation_hook(
			{{PLUGIN_PREFIX}}_PATH,
			array( $this, 'register_activation_hook_roles_and_capabilities' )
		);
		register_deactivation_hook(
			{{PLUGIN_PREFIX}}_PATH,
			array( $this, 'register_deactivation_hook_roles_and_capabilities' )
		);
	}

	/**
	 * When plugin is activated, register plugin roles and add capabilities.
	 */
	public function register_activation_hook_roles_and_capabilities()
	{
		$this->addOrRemoveRolesAndCapabilities();
	}
	
	public function register_deactivation_hook_roles_and_capabilities()
	{
		$this->addOrRemoveRolesAndCapabilities( true );
	}

	public function addOrRemoveRolesAndCapabilities( bool $remove = false )
	{
		foreach ( $this->roles as $role_snake => $role_name ) {
			if ( $remove ) {
				remove_role( $role_snake, $role_name );
			} else {
				add_role( $role_snake, $role_name );
			}
		}

		unset( $role_snake, $role_name );

		foreach ( $this->capabilities as $c ) {
			$roles = ( is_array( $c['roles'] ) ) ?
				$c['roles'] :
				array( $c['roles'] );

			$capabilities = ( is_array( $c['capabilities'] ) ) ?
				$c['capabilities'] :
				array( $c['capabilities'] );

			foreach ($roles as $the_role ) {
				$role = get_role( $the_role );

				if ( empty( $role ) ) {
					continue;
				}

				foreach ( $capabilities as $capability ) {
					if ( $remove ) {
						$role->remove_cap( $capability );
					} else {
						$role->add_cap( $capability );
					}
				}
			}
		}

		unset( $roles, $the_role, $role, $c, $capabilities, $capability );
	}
}