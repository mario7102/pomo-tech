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
		$("body").on("userloggedin", "#userInfo", function(event){
			event.preventDefault;
			that.getUserInfo();
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
	},

	getUserInfo: function(){
		var token = sessionStorage.access_token;
		$.ajax({
			type: "GET",
			beforeSend: function(request) {
				request.setRequestHeader("GITHUB-JWT", sessionStorage.access_token);
			},
			url:"/auth/getuserinfo",
			success: function(json) {
				$("#userInfo").html('<img alt="'+json.name+'" src="'+json.avatar_url+'" height="28" style="border-radius: 50%;"> '+json.name);
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