<template lang="">
<div>
    <h1>Home</h1>
    <div>
        Pleace write your comment
        <quill-editor ref="quillComponent"></quill-editor>
        <!-- <textarea v-model="post.content" name="" id="" cols="30" rows="10"></textarea> -->
        <button @click="postCreate()" class="btn">Submit</button>
    </div>

    <CommentTree :replyPostId="replyPostId" :posts="posts"></CommentTree>
    <div v-if="!posts.length">
        <h3>No any post(</h3>
    </div>

</div>
</template>

<script>
import axios from 'axios';
import QuillEditor from "./../layouts/QuillEditor.vue";
import CommentTree from './../layouts/CommentTree.vue';

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
        QuillEditor,
        CommentTree
    },
    methods: {
        postCreate(postId = null) {
            const post = {
                content: ''
            };
            let fd = new FormData();

            if (postId) {
                post.content = this.$refs.quillReply[0].quill.root.innerHTML;
                post.parent_id = postId;
            } else {
                post.content = this.$refs.quillComponent.quill.root.innerHTML;

                this.$refs.quillComponent.tempFiles.forEach((file, index) => {
                    fd.append(`files[${index}]`, file);
                });
            }

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
    },

    mounted() {
        this.getPosts()
    },

}
</script>

<style lang="scss">
.files-container {
    display: flex;
    flex-wrap: wrap;
    padding: 10px;
    margin: 5px;

    .file-element {
        position: relative;
        max-width: 240;
        max-height: 320;
        padding: 10px;
    }

    .cross-delete {
        top: 0;
        right: 0;
    }
}
</style>
