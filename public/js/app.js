Vue.component("comment-item", {
	name: "comment-item",
	props: ['comment'],
	data: {
		commentLikes: 0
	},
	template:
	'<ol>' +
	  '<li class="card-text">{{ comment.user.personaname }} - ' +
	    '<small class="text-muted">{{comment.text}}</small>' +
		'<div class="likes" @click="addLike(comment.id, \'comment\')">' +
		  '<div class="heart-wrap" v-if="!commentLikes">' +
		    '<div class="heart">' +
			  '<svg class="bi bi-heart" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">' +
			    '<path fill-rule="evenodd" d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 01.176-.17C12.72-3.042 23.333 4.867 8 15z" clip-rule="evenodd"/>' +
			  '</svg>' +
		    '</div>' +
		    '<span>{{comment.likes}}</span>' +
		  '</div>' +
		  '<div class="heart-wrap" v-else>' +
	 	    '<div class="heart">' +
			  '<svg class="bi bi-heart-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">' +
			    '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z" clip-rule="evenodd"/>' +
			  '</svg>' +
		    '</div>' +
		    '<span>{{commentLikes}}</span>' +
	      '</div>' +
	    '</div>' +
	    '<ol v-if="comment.comments && comment.comments.length > 0">' +
	      '<comment-item v-for="comment in comment.comments" v-bind:comment="comment" v-bind:key="comment.id"/>' +
	    '</ol>' +
	  '</li>' +
	'</ol>',
	methods: {
		addLike: function (id, type) {
			var self = this;
			axios
                .get('/main_page/like/' + id + '/' + type)
                .then(function (response) {
					if(response.data.type == 'comment') {
						self.comment.likes = response.data.likes;
					} else {
						self.postLikes = response.data.likes;
					}
				})

		}
	}
})

var app = new Vue({
	el: '#app',
	data: {
		login: '',
		pass: '',
		post: false,
		invalidLogin: false,
		invalidPass: false,
		invalidSum: false,
		posts: [],
		addSum: 0,
		amount: 0,
		postLikes: 0,
		commentLikes: 0,
		commentText: '',
		packs: [
			{
				id: 1,
				price: 5
			},
			{
				id: 2,
				price: 20
			},
			{
				id: 3,
				price: 50
			},
		],
	},
	computed: {
		test: function () {
			var data = [];
			return data;
		}
	},
	created(){
		var self = this
		axios
			.get('/main_page/get_all_posts')
			.then(function (response) {
				self.posts = response.data.posts;
			})
	},
	methods: {
		logout: function () {
			console.log ('logout');
		},
		logIn: function () {
			var self= this;
			if(self.login === ''){
				self.invalidLogin = true
			}
			else if(self.pass === ''){
				self.invalidLogin = false
				self.invalidPass = true
			}
			else{
				self.invalidLogin = false
				self.invalidPass = false
				axios.post('/main_page/login', {
					login: self.login,
					password: self.pass
				})
					.then(function (response) {
                        setTimeout(function () {
                            $('#loginModal').modal('hide');
                            location.reload();
                        }, 500);
					})
			}
		},
		fiilIn: function () {
			var self= this;
			if(self.addSum === 0){
				self.invalidSum = true
			}
			else{
				self.invalidSum = false
				axios.post('/main_page/add_money', {
					sum: self.addSum,
				})
					.then(function (response) {
						setTimeout(function () {
							$('#addModal').modal('hide');
						}, 500);
					})
			}
		},
		openPost: function (id) {
			var self= this;
			axios
				.get('/main_page/get_post/' + id)
				.then(function (response) {
					self.post = response.data.post;
					if(self.post){
						console.log(self.post)
						setTimeout(function () {
							$('#postModal').modal('show');
						}, 500);
					}
				})
		},
		addLike: function (id, type) {
			var self = this;
			axios
				.get('/main_page/like/' + id + '/' + type)
				.then(function (response) {
					if(response.data.type == 'comment') {
						self.commentLikes = response.data.likes;
					} else {
						self.postLikes = response.data.likes;
					}
				})

		},
		buyPack: function (id) {
			var self= this;
			axios.post('/main_page/buy_boosterpack', {
				id: id,
			})
				.then(function (response) {
					self.amount = response.data.amount
					if(self.amount !== 0){
						setTimeout(function () {
							$('#amountModal').modal('show');
						}, 500);
					}
				})
		},
        addComment: function (post_id, type) {
            var self = this;

            axios.get('/main_page/comment/' + post_id + '/' + self.commentText + '/' + type)
                .then(function (response) {
                    self.post = response.data.post;
                    if(self.post){
                        self.commentText = ''
                        setTimeout(function () {
                            $('#postModal').modal('show');
                        }, 500);
                    }
                })

            event.preventDefault()
        }
	}
});

