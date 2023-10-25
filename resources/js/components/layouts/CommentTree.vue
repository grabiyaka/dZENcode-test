<template>
<div>
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
                <div class="files-container" v-if="post.files">
                    <div class="file-element" v-for="(file, index) in post.files" :key="file.id">
                        <img v-if="isImage(file) || isGif(file)" :src="'/storage/'+file.path" alt="Image" />
                        <pre v-else-if="isTextFile(file)">{{ file.content }}</pre>
                        <span v-else>{{ file.name }}</span>
                        <button class="cross-delete" @click="deleteFile(file, post.id)">&#10005</button>
                    </div>
                </div>
            </div>

            <div class="post-control-panel">
                <button @click="replyPostId = post.id, $parent" class="btn reply-btn" v-if="post.id != replyPostId">Reply</button>

                <div class="replyPost" v-if="post.id == replyPostId">
                    <quill-editor :post="post" :ref="'quillReply'"></quill-editor>

                </div>
            </div>
            <CommentTree v-if="post.posts" :posts="post.posts"></CommentTree>
        </div>
    </div>
</div>
</template>

<script>
import QuillEditor from "./QuillEditor.vue";

export default {
    props: {
        posts: Array,
        replyPostId: null
    },
    data() {
        return {}
    },
    components: {
        QuillEditor
    },
    methods: {
        postCreate(postId = null) {
            let fd = new FormData()
            const post = {
                content: ''
            }

            console.log(this.$refs);
            if (postId) {
                post.content = this.$refs.quillReply[0].quill.root.innerHTML;
                post.parent_id = postId

                this.$refs.quillReply[0].tempFiles.forEach((file, index) => {
                    fd.append(`files[${index}]`, file);
                    console.log(file);
                });
            } else post.content = this.$refs.quillComponent.quill.root.innerHTML;

            for (let key in post) {
                if (post.hasOwnProperty(key)) {
                    fd.append(key, post[key]);
                }
            }
            if (post.content) {
                axios.get("/sanctum/csrf-cookie").then((response) => {
                    axios
                        .post("/api/post", fd)
                        .then((res) => {
                            if (postId) {
                                console.log(res.data);
                            } else {
                                console.log(res.data);
                                // this.post.content = ''
                                // this.$refs.quillComponent.quill.root.innerHTML = ''
                                // this.posts.unshift(res.data.data)
                            }

                        })
                        .catch((err) => {});
                });
            } else {
                alert('error');
            }
        },
        deletePost(id) {
            axios
                .get("/sanctum/csrf-cookie")
                .then((res) => {
                    if (confirm('delete post?')) {
                        axios
                            .delete(`/api/post/${id}`)
                            .then((res) => {
                                this.removePostById(this.$store.getters.getPosts, id)
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
        isImage(file) {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

            const fileExtension = file.name.split('.').pop().toLowerCase();

            return imageExtensions.includes(fileExtension);
        },

        isGif(file) {
            return this.isImage(file) && file.name.toLowerCase().endsWith('.gif');
        },

        isTextFile(file) {
            const textFileExtensions = ['txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            return textFileExtensions.includes(fileExtension);
        },
        deleteFile(file, id) {
            if (confirm('Delte file?')) {
                console.log()
                axios.get("/sanctum/csrf-cookie").then((response) => {
                    axios
                        .delete(`/api/file/${file.id}`)
                        .then((res) => {
                            this.removeFileByIdAndPostId(this.$store.getters.getPosts, file.id, id)
                        })
                        .catch((err) => {});
                });
            }
        },
        removeFileByIdAndPostId(posts, fileId, postId) {
            for (const post of posts) {

                if (post.id === postId) {

                    if (post.files) {
                        const index = post.files.findIndex(file => file.id === fileId);
                        if (index !== -1) {
                            post.files.splice(index, 1);
                            return posts;
                        }
                    }

                }

                if (post.posts) {

                    const updatedPosts = this.removeFileByIdAndPostId(post.posts, fileId, postId);
                    if (updatedPosts !== post.posts) {
                        post.posts = updatedPosts;
                        return posts;
                    }

                }
            }
            return posts;
        },
        removePostById(posts, postId) {
            for (let i = posts.length - 1; i >= 0; i--) {
                const post = posts[i];
                if (post.id === postId) {
                    posts.splice(i, 1);
                    return posts; 
                }

                if (post.posts) {
                    const updatedPosts = this.removePostById(post.posts, postId);
                    if (updatedPosts !== post.posts) {
                        post.posts = updatedPosts;
                        return posts;
                    }
                }
            }
            return posts;
        }

    }
};
</script>

<style lang="scss">
.block-posts {
    padding: 10px 0 10px 10px;
    margin: 10px 3px;
    background-color: #f5f5f5;

    .block-post {
        position: relative;
        margin: 10px 0;
        padding: 0px 0px 10px 10px;
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

}
</style>
