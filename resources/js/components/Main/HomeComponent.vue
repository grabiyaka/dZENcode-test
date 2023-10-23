<template lang="">
<div>
    <h1>Home Component</h1>
    <div>
        Pleace write your comment
        <textarea v-model="post.content" name="" id="" cols="30" rows="10"></textarea>
        <button @click="postCreate()" class="btn">Submit</button>
    </div>
    <div class="block-posts">
        <div v-for="post in posts" class="block-post">
            {{ post.user_name }}:<textarea v-model="post.content" cols="30" rows="10"></textarea>
            <button @click="updatePost(post)" class="btn">Update</button>
            <button @click="deletePost(post.id)" class="btn">Delete</button>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            post: {
                content: ''
            },
            posts: []
        }
    },
    methods: {
        postCreate() {
            if (this.post.content) {
                axios.get("/sanctum/csrf-cookie").then((response) => {
                    axios
                        .post("/api/post", this.post)
                        .then((res) => {
                            this.post.content = ''
                            this.posts.unshift(res.data.data)
                        })
                        .catch((err) => {});
                });
            } else {
                alert('error');
            }
        },
        getPosts() {
            try {
                axios
                    .get(`api/posts/`)
                    .then((res) => {
                        console.log(res.data);
                        this.posts = res.data;
                    })
                    .catch((err) => {});
            } catch (error) {}
        },
        deletePost(id) {
            axios
                .get("/sanctum/csrf-cookie")
                .then((res) => {
                    if (confirm('delete post?')) {
                        axios
                            .delete(`/api/post/${id}`)
                            .then((res) => {
                                this.posts = this.posts.filter(el => el.id != id);
                            })
                            .catch((err) => {});
                    }

                })
                .catch((err) => {});
        },
        updatePost(post) {
          axios.get("/sanctum/csrf-cookie").then((response) => {
            axios
              .patch(`/api/post/${post.id}`, post)
              .then((res) => {
                alert('saved!');
              })
              .catch((err) => {});
          });
        },
    },

    mounted() {
        this.getPosts()
    }
}
</script>

<style lang="">

</style>
