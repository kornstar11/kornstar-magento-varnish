# 
# This VCL is meant to used with the Cp_Varnish Module (last update 04/30/2012, by Ben Kornmeier)
# server.
# 
backend default {
  .host = "127.0.0.1";
  .port = "80";
}
acl purge_acl {
   "localhost";
   "127.0.0.1";
   "50.116.8.200"; # Your IP
}

sub vcl_recv {
	# Purge and bans START
	if(req.url ~ "flushallsearch") {
                ban("req.url ~ catalogsearch");
                error 200 "Ban on search added";
        }
        if(req.url ~ "flushsearch") {
                ban("req.url ~ "+req.http.clearThis+" && req.url ~ catalogsearch");
                error 200 "Ban on search added for:"+req.http.clearThis+":";
        }
        if (req.url ~ "flushall") {
                ban("req.http.host ~ .");
                error 200 "Ban added";
        }
        if (req.request   == "DELETE") {
                if (!client.ip ~ purge_acl) {
                        error 405 "Not allowed.";
                }
                return (lookup);
        }
	# Purge and bans END

	# Varnish expects the 'beniscool' cookie to be set in ordeer to determine if it should use cached content or not.
	# Having the cookie set means pass traffic to the backend.
	# Not having the cookie means try to serve cached content.
	if (req.url ~ "admin") {
		set req.http.beniscool = "1";
		return(pass);
	}
	if (req.url ~ "checkout" || req.url ~ "customer") {
		set req.http.beniscool = "1";
		return(pass);
	}
	# For Nginx servers I recomend not storing static content in Varnish. Nginx is already fast enough, no need to waste cache space. On Apache it is a different story
		#if ((req.request == "GET" && req.url ~ "^/media/") || (req.request == "GET" && req.url ~ "^/skin/") || (req.url ~ "^/directory/")) {
		#	set req.http.beniscool = "1";
		#	return(pass);
		#}
	# Magento has set the 'beniscool' cookie so pass trafic back to the backend!
	if (req.http.cookie ~ "beniscool") {
		set req.http.beniscool = "1";
		return(pass);
	}
	# We made it here attempt a lookup!!
	unset req.http.Cookie;
	return(lookup);
}
sub vcl_fetch {
    if(beresp.status >= 500) {
	 # Do not cache errors
	 set beresp.http.ShouldCache = "PASS SERVER ERROR";
	 return(hit_for_pass);
    }
    if ((req.url ~ "^/media/") || (req.url ~ "^/skin/")) {
       remove beresp.http.Cache-Control;
       set beresp.http.Cache-Control = "max-age=2629743"; # We could do this on the server or Varnish, I decided to do it here.

    }
    if (req.http.beniscool){
	set beresp.http.ShouldCache = "PASS";
	return(hit_for_pass);
    }
    # The lines below normalize the response from the backend so it is not unique and can be hit
    unset beresp.http.beniscool; # Remove the cookie we set
    unset beresp.http.Cache-Control;
    unset beresp.http.Expires;
    unset beresp.http.Pragma;
    unset beresp.http.Cache;
    unset beresp.http.Server;
    unset beresp.http.Set-Cookie;
    unset beresp.http.Age;
    set beresp.ttl = 7d;

    set beresp.http.ShouldCache = "DELV";
    return (deliver);
    
    # Varnish determined the object was not cacheable
    if (beresp.ttl <= 0s) {
        set beresp.http.X-Cacheable = "NO:Not Cacheable";
	set beresp.http.magicmarker = "1";
    # You don't wish to cache content for logged in users
    } elsif (req.http.Cookie ~ "(UserID|_session)") {
        set beresp.http.X-Cacheable = "NO:Got Session";
        return(hit_for_pass);
    
    # You are respecting the Cache-Control=private header from the backend
    } elsif (beresp.http.Cache-Control ~ "private") {
        set beresp.http.X-Cacheable = "NO:Cache-Control=private";
        return(hit_for_pass);
    
    # Varnish determined the object was cacheable
    } else {
#    	set beresp.ttl = 300;
	 #unset beresp.http.Cookie;
	 #unset beresp.http.set-cookie;
        set beresp.http.X-Cacheable = "YES";
    }
    
    # ....
    
    return(deliver);
}
# Purge START
sub vcl_hit {
        if (req.request == "DELETE") {
                purge;
                error 200 "Purged.";
        }
}

sub vcl_miss {
        if (req.request == "DELETE") {
                purge;
                error 200 "Purged.";
        }
}
# Purge END
sub vcl_deliver {
                if (resp.http.magicmarker) {
                        /* Remove the magic marker */
                        unset resp.http.magicmarker;

                        /* By definition we have a fresh object */
                        set resp.http.age = "0";
                }
        }
# 
# Below is a commented-out copy of the default VCL logic.  If you
# redefine any of these subroutines, the built-in logic will be
# appended to your code.
# sub vcl_recv {
#     if (req.restarts == 0) {
# 	if (req.http.x-forwarded-for) {
# 	    set req.http.X-Forwarded-For =
# 		req.http.X-Forwarded-For + ", " + client.ip;
# 	} else {
# 	    set req.http.X-Forwarded-For = client.ip;
# 	}
#     }
#     if (req.request != "GET" &&
#       req.request != "HEAD" &&
#       req.request != "PUT" &&
#       req.request != "POST" &&
#       req.request != "TRACE" &&
#       req.request != "OPTIONS" &&
#       req.request != "DELETE") {
#         /* Non-RFC2616 or CONNECT which is weird. */
#         return (pipe);
#     }
#     if (req.request != "GET" && req.request != "HEAD") {
#         /* We only deal with GET and HEAD by default */
#         return (pass);
#     }
#     if (req.http.Authorization || req.http.Cookie) {
#         /* Not cacheable by default */
#         return (pass);
#     }
#     return (lookup);
# }
# 
# sub vcl_pipe {
#     # Note that only the first request to the backend will have
#     # X-Forwarded-For set.  If you use X-Forwarded-For and want to
#     # have it set for all requests, make sure to have:
#     # set bereq.http.connection = "close";
#     # here.  It is not set by default as it might break some broken web
#     # applications, like IIS with NTLM authentication.
#     return (pipe);
# }
# 
# sub vcl_pass {
#     return (pass);
# }
# 
# sub vcl_hash {
#     hash_data(req.url);
#     if (req.http.host) {
#         hash_data(req.http.host);
#     } else {
#         hash_data(server.ip);
#     }
#     return (hash);
# }
# 
# sub vcl_hit {
#     return (deliver);
# }
# 
# sub vcl_miss {
#     return (fetch);
# }
# 
# sub vcl_fetch {
#     if (beresp.ttl <= 0s ||
#         beresp.http.Set-Cookie ||
#         beresp.http.Vary == "*") {
# 		/*
# 		 * Mark as "Hit-For-Pass" for the next 2 minutes
# 		 */
# 		set beresp.ttl = 120 s;
# 		return (hit_for_pass);
#     }
#     return (deliver);
# }
# 
# sub vcl_deliver {
#     return (deliver);
# }
# 
# sub vcl_error {
#     set obj.http.Content-Type = "text/html; charset=utf-8";
#     set obj.http.Retry-After = "5";
#     synthetic {"
# <?xml version="1.0" encoding="utf-8"?>
# <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
#  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
# <html>
#   <head>
#     <title>"} + obj.status + " " + obj.response + {"</title>
#     <META HTTP-EQUIV=Refresh CONTENT='5; URL=http://discountdecorating.com'>
#   </head>
#   <body>
#	<span style='display:none;' class='debug-info'>"} + obj.status + " " + obj.response + {" </span>
#	<pre>
#   .-. _                                .-. 
#   : ::_;                              .' `.
# .-' :.-. .--.  .--.  .--. .-..-.,-.,-.`. .'
#' .; :: :`._-.''  ..'' .; :: :; :: ,. : : : 
#`.__.':_;`.__.'`.__.'`.__.'`.__.':_;:_; :_; 
#                                            
#
#   .-.                              .-.  _             
#   : :                             .' `.:_;            
# .-' : .--.  .--.  .--. .--.  .--. `. .'.-.,-.,-. .--. 
#' .; :' '_.''  ..'' .; :: ..'' .; ; : : : :: ,. :' .; :
#`.__.'`.__.'`.__.'`.__.':_;  `.__,_;:_; :_;:_;:_;`._. ;
#                                                  .-. :
#                                                  `._.'
#	</pre>
#	<p> Due to a sale the site is currently overloaded.</p>
#	
#	<p> Please call us for help: </p>
#	<p> (866) 797 7575</p>
#	<p> (636) 587 8868</p>
#	
#     <p>XID: "} + req.xid + {"</p>
#     <hr>
#   </body>
# </html>
# "};
#     return (deliver);
# }
# 
# sub vcl_init {
# 	return (ok);
# }
# 
# sub vcl_fini {
# 	return (ok);
# }
