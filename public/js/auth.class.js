var AuthClient = function() {
	this.bindEventsOn();
};

AuthClient.prototype = {
	constructor: AuthClient,
	classe: 'AuthClient',
	
	bindEventsOn: function() {
		var that = this;
		$("body").on("click", this.selectors.githubLogin, function(event){
			event.preventDefault;
			that.getState();
		});
		$("body").on("click", this.selectors.logout, function(event){
			event.preventDefault;
			that.logout();
		});
	},

	getState: function(){
		var that = this;
		$.ajax({
			url:"/auth/getstate",
			success: function(json) {
				window.location = "https://github.com/login/oauth/authorize?client_id="+json.params.cid+"&scope="+json.params.scope+"&state="+json.params.state;
			},
		});
	},

	logout: function(){
		sessionStorage.clear();
		$.ajax({
			url:"/auth/logout",
			success: function(json) {
				window.location = "/";
			},
		});
	}
};

AuthClient.prototype.selectors = {
	githubLogin : "#githubLogin",
	logout : "a.logout"
};

$().ready(function(e){
	authClient = new AuthClient();
});