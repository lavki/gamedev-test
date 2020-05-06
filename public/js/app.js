Vue.component('comment-item', {
	name: "comment-item",
	props: ['comment'],
	template:
	'<ul>' +
	  '<li class="card-text">{{ comment.user.personaname }} - ' +
	    '<small class="text-muted">{{comment.text}}</small>' +
	    '<ul v-if="comment.comments && comment.comments.length > 0">' +
		  '<comment-item v-for="comment in comment.comments" v-bind:comment="comment" v-bind:key="comment.id"/>' +
		'</ul>' +
	  '</li>' +
	'</ul>'
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
		likes: 0,
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
		addLike: function (id) {
			var self= this;
			axios
				.get('/main_page/like/' + id)
				.then(function (response) {
					self.likes = response.data.likes;
					console.log(self.likes);
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
        addComment: function (post_id) {
            var self = this;

            axios.get('/main_page/comment/' + post_id + '/' + self.commentText)
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

