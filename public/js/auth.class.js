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
	},

	getState: function(){
		var that = this;
		$.ajax({
			url:"/auth/getstate",
			success: function(json) {
				window.location = "https://github.com/login/oauth/authorize?client_id="+json.params.cid+"&scope="+json.params.scope+"&state="+json.params.state;
			},
		});
	}
};

AuthClient.prototype.selectors = {
	githubLogin : "#githubLogin"
};

$().ready(function(e){
	authClient = new AuthClient();
});