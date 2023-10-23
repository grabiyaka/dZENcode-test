<template lang="">
<div>
    <h1>Home</h1>
    <div>
        Pleace write your comment
        <quill-editor ref="quillComponent"></quill-editor>
        <!-- <textarea v-model="post.content" name="" id="" cols="30" rows="10"></textarea> -->
        <button @click="postCreate()" class="btn">Submit</button>
    </div>

    <div class="block-posts" v-if="posts.length">
        <div v-for="post in posts" class="block-post">
            <button @click="deletePost(post.id)" class="cross-delete">&#10005</button>
            <div class="user-info">

                <div class="user-avatar">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkgA_xdu7bbkn44ysPIKt7scHEXEqsiIMUWQ&usqp=CAU" alt="User Avatar" />
                </div>

                <span class="user-name">{{ post.user_name }}:</span>
                <span class="post-time">{{ formatDateTime(post.created_at) }}</span>
            </div>

            <div class="post-content">
                <div class="post-text" v-html="post.content"></div>
            </div>

            <div class="post-control-panel">
                <button @click="replyPostId = post.id" class="btn reply-btn" v-if="post.id != replyPostId">Reply</button>

                <div class="replyPost" v-if="post.id == replyPostId">
                    <quill-editor :ref="'quillReply'"></quill-editor>
                    <button @click="postCreate(post.id)" class="btn reply-btn">Reply</button>
                </div>
            </div>

        </div>
    </div>
    <div v-if="!posts.length">
        <h3>No any post(</h3>
    </div>

</div>
</template>

<script>
import axios from 'axios';
import QuillEditor from "./../layouts/QuillEditor.vue";

export default {
    data() {
        return {
            post: {
                content: ''
            },
            posts: [],
            replyPostId: null,
            reply: ''
        }
    },
    components: {
        QuillEditor
    },
    methods: {
        postCreate(postId = null) {
            const post = {
                content: ''
            }

            console.log(this.$refs);
            if (postId) {
                post.content = this.$refs.quillReply[0].quill.root.innerHTML;
                post.parent_id = postId
                console.log(post);
            } else post.content = this.$refs.quillComponent.quill.root.innerHTML;

            if (post.content) {
                axios.get("/sanctum/csrf-cookie").then((response) => {
                    axios
                        .post("/api/post", post)
                        .then((res) => {
                            if (postId) {

                            } else {
                                this.post.content = ''
                                this.$refs.quillComponent.quill.root.innerHTML = ''
                                this.posts.unshift(res.data.data)
                            }

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
        formatDateTime(dateTimeString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const dateTime = new Date(dateTimeString);
            return dateTime.toLocaleString(undefined, options);
        },
    },

    mounted() {
        this.getPosts()
    },

}
</script>

<style lang="scss">
.block-posts {
    border: 1px solid #ccc;
    padding: 10px;
    margin: 10px;
    background-color: #f5f5f5;

    .block-post {
        position: relative;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #fff;

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;

            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                overflow: hidden;
                margin-right: 10px;

                img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
            }

            .user-name {
                font-weight: bold;
                color: #007bff;
            }

            .post-time {
                margin-left: auto;
                color: #888;
            }
        }

        .post-content {
            .post-text {
                margin-top: 5px;
            }
        }

        .post-control-panel {
            margin-top: 10px;

            .btn {
                background-color: #007bff;
                color: #fff;
                padding: 5px 10px;
                border: none;
                cursor: pointer;
                margin-right: 10px;
                border-radius: 5px;
            }

            .delete-btn {
                background-color: #d9534f;
            }

            .reply-btn {
                background-color: #5bc0de;
            }
        }
    }

    .post-time {
        font-style: italic;
        color: #888;
    }

    .cross-delete {
        position: absolute;
        top: -10px;
        right: -10px;
        font-size: 20px;
        color: whitesmoke;
        border: none;
        cursor: pointer;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: transform 0.5s;
        background: #d9534f;
    }

    .cross-delete:hover {
        transform: rotate(180deg);
    }
}
</style>
