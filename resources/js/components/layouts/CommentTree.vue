<style lang="scss">
.block-posts {
    background-color: #f5f5f5;

    .block-post {
        position: relative;
        margin: 10px 0;
        padding: 0 0 10px 10px;
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
        }
    }

    .post-time {
        font-style: italic;
        color: #888;
    }

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

    .gray-btn {
        background-color: #808080;
    }

    .img-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9;
        transition: all ease .5s;

        opacity: 0;
        visibility: hidden;
    }

    .img-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s;
        cursor: pointer;
        transition: all ease .5s;

        opacity: 0;
        visibility: hidden;
    }

    .img-container img {
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
        margin-top: 90vh;
        transition: all ease .5s;
        border-radius: 5px;

        opacity: 0;
        visibility: hidden;
    }

    /* Анимация появления фона */
    .img-container.active,
    .img-background.active,
    img.active {
        opacity: 1;
        visibility: visible;
        margin: 0;
    }

    .img-background.active {
        opacity: 0.3;
    }

    .reply-block {
        padding: 0 5px;
        background: #80808050;
    }

}
</style>

<template>
<div>
    <div class="block-posts" v-if="posts.length">
        <div v-for="(post, index) in posts" class="block-post">
            <div class="reply-block" v-if="parentPost?.parent_id">
                <p>{{ removeTagsAndTruncate(parentPost.content) }}...</p>
            </div>
            <button v-if="$store.getters.getUser?.id == post.user_id" @click="deletePost(post.id)" class="cross-delete">&#10005</button>

            <div class="user-info">

                <div class="user-avatar">
                    <img v-if="!$store.getters.getUser?.avatar" src="/images/default-avatar.png" alt="User Avatar" />
                    <img v-if="$store.getters.getUser?.avatar" :src="'/storage/'+$store.getters.getUser?.avatar" alt="User Avatar" />
                </div>

                <span class="user-name">{{ post.user_name }}:</span>
                <span class="post-time">{{ formatDateTime(post.created_at) }}</span>
            </div>

            <div class="post-content">
                <div class="post-text" v-html="post.content"></div>
                <div v-if="post.files" class="files-container">
                    <div class="file-element" v-for="(file, index) in post.files" :key="file.id" :style="{'background-image': isImage(file) || isGif(file) ? `url(${'/storage/'+file.path})` : 'none'}">
                        <div v-if="isImage(file) || isGif(file)" @click="openImg('/storage/'+file.path)" ></div>
                        <div @click="openModalTxt('/storage/'+file.path)" v-else-if="isTextFile(file)"><i class="bi bi-filetype-txt"></i></div>
                        <p>{{ file.name }}</p>
                        <button v-if="$store.getters.getUser?.id == post.user_id" class="cross-delete" @click="deleteFile(file, post.id)">&#10005</button>
                    </div>
                </div>
            </div>
            <div :class="{active: img.active}" class="img-container">
                <img :class="{active: img.active}" :src="img.path" alt="" @click="img.active = false">
                <div :class="{active: img.active}" class="img-background" @click="img.active = false"></div>
            </div>
            <div v-if="txt.active" :class="{active: txt.active}" class="img-container">
                <text-viewer :filePath="txt.path"></text-viewer>
                <div :class="{active: txt.active}" class="img-background" @click="txt.active = false"></div>
            </div>

            <div class="post-control-panel">
                <button @click="$store.commit('setReplyId', post.id)" class="btn reply-btn" v-if="post.id !== $store.getters.getReplyId && $store.state.token.token">Reply</button>

                <div class="replyPost" v-if="post.id == $store.getters.getReplyId">
                    <quill-editor :post="post" :ref="'quillReply'"></quill-editor>
                </div>
            </div>
            <div v-if="post.posts">
                <CommentTree :parentPost="post" :posts="post.posts"></CommentTree>
                <button class="btn gray-btn" v-if="post.more_posts" @click="downloadMoreComments(post.id, index)">Download more comments...</button>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import QuillEditor from "./QuillEditor.vue";
import TextViewer from "./TextViewer.vue"
import PopupComponent from "./PopupComponent.vue";

export default {
    props: {
        posts: Object,
        parentPost: Object
    },
    data() {
        return {
            img: {
                active: false,
                path: ''
            },
            txt: {
                active: false,
                path: ''
            }

        }
    },
    components: {
        QuillEditor,
        TextViewer,
        PopupComponent
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
                                this.post.content = ''
                                this.quill.root.innerHTML = ''
                                const result = this.posts.unshift(res.data.data)
                                this.$store.commit('setPosts', result)
                                console.log(this.$store.getters.getPosts);
                            }

                        })
                        .catch((err) => {});
                });
            } else {
                alert('error');
            }
        },
        downloadMoreComments(id, index) {
            axios
                .post(`/api/post/childs/${id}`)
                .then((res) => {
                    res.data.forEach(el => {
                        this.$store.commit('addPostToParent', {
                            parentId: id,
                            newPost: el
                        });
                    });
                    this.posts[index].more_posts = false
                })
                .catch((err) => {});
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
        },
        openImg(path) {
            this.img.path = path
            this.img.active = true
        },
       
        downloadFile() {
            // Создаем ссылку для скачивания файла
            const blob = new Blob([this.fileContent], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'textfile.txt'; // Задайте имя файла
            a.click();
            window.URL.revokeObjectURL(url);
        },
        openModalTxt(path) {
            console.log(path);
            this.txt.path = path
            this.txt.active = true
        },
      
        removeTagsAndTruncate(htmlContent) {
            const div = document.createElement('div');
            div.innerHTML = htmlContent;

            const textContent = div.textContent;
            return textContent.slice(0, 20);
        }

    }
};
</script>
