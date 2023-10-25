<template lang="">
<div>
    <h1>Home</h1>
    <div>
        Pleace write your comment
        <quill-editor ref="quillComponent"></quill-editor>
    </div>

    <CommentTree :replyPostId="replyPostId" :posts="$store.getters.getPosts"></CommentTree>
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
        getPosts() {
            try {
                axios
                    .get(`api/posts/`)
                    .then((res) => {
                        this.$store.commit('setPosts', res.data)
                        console.log(this.$store.getters.getPosts);
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
