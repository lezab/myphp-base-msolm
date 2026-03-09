##<?php
##namespace $nsp"."core;
##
##use \\$nsp"."exceptions\\LdapConnectionProviderException;
##
##class LdapConnectionProvider {
##	
##	protected static \$ldap;
##	
##	/**
##	 * @throws LdapConnectionProviderException
##	 * @return \\MLdap
##	 */
##	public static function getInstance(){
##		if (!isset(self::\$ldap)){
##			
##			include(__DIR__.\"/__ldap_params.conf.php\");
##			
##			try {
##				self::\$ldap = new \\mlib\\net\\ldap\\MLdap(\$ldap_address, \$ldap_port, \$ldap_bind_dn, \$ldap_bind_password);
##			}
##			catch (\\mlib\\net\\ldap\\MLdapException \$e) {
##				throw new LdapConnectionProviderException(\$e);
##			}
##		}
##		return self::\$ldap;
##	}
##}
#?>