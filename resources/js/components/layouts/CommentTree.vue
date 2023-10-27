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
        background-color: rgba(0, 0, 0, 0.527);
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

    .imgFlipBtn {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(190, 190, 190, 0.616);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        color: #000000;
        cursor: pointer;
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
                    <img v-if="!post.avatar" src="/images/default-avatar.png" alt="User Avatar" />
                    <img v-if="post.avatar" :src="'/storage/'+post.avatar" alt="User Avatar" />
                </div>

                <span class="user-name">{{ post.user_name }}:</span>
                <span class="post-time">{{ formatDateTime(post.created_at) }}</span>
            </div>

            <div class="post-content">
                <div class="post-text" v-html="post.content"></div>
                <div v-if="post.files" class="files-container">
                    <div class="file-element" v-for="(file, index) in post.files" :key="file.id" :style="{'background-image': isImage(file) || isGif(file) ? `url(${'/storage/'+file.path})` : 'none'}">
                        <div v-if="isImage(file) || isGif(file)" @click="openImg('/storage/'+file.path)"></div>
                        <div @click="openModalTxt('/storage/'+file.path)" v-else-if="isTextFile(file)"><i class="bi bi-filetype-txt"></i></div>
                        <p>{{ file.name }}</p>
                        <button v-if="$store.getters.getUser?.id == post.user_id && (post.content != '<p><br></p>' || post.files.length > 1) " class="cross-delete" @click="deleteFile(file, post.id)">&#10005</button>
                    </div>
                </div>
            </div>
            <div :class="{active: img.active}" class="img-container">
                <img :class="{active: img.active}" :style="img.style" :src="img.path" alt="" @click="closeImg">
                <button class="imgFlipBtn" @click="changeImgStyle"><i class="bi bi-arrow-clockwise"></i></button>
                <div :class="{active: img.active}" class="img-background" @click="closeImg"></div>
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

export default {
    props: {
        posts: Object,
        parentPost: Object
    },
    data() {
        return {
            img: {
                active: false,
                path: '',
                style: {
                    transform: 'none',
                    transition: 'all ease 0.5s'
                },
                rotate: 0
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
    },
    methods: {
        postCreate(postId = null) {
            let fd = new FormData()
            const post = {
                content: ''
            }

            if (postId) {
                post.content = this.$refs.quillReply[0].quill.root.innerHTML;
                post.parent_id = postId

                this.$refs.quillReply[0].tempFiles.forEach((file, index) => {
                    fd.append(`files[${index}]`, file);
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
                            } else {
                                this.post.content = ''
                                this.quill.root.innerHTML = ''
                                const result = this.posts.unshift(res.data.data)
                                this.$store.commit('setPosts', result)
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
            this.txt.path = path
            this.txt.active = true
        },

        changeImgStyle() {
            this.img.rotate += 90

            this.img.style.transform = `rotate(${this.img.rotate}deg)`
        },

        removeTagsAndTruncate(htmlContent) {
            const div = document.createElement('div');
            div.innerHTML = htmlContent;

            const textContent = div.textContent;
            return textContent.slice(0, 20);
        },

        closeImg(){
            this.img.active = false 
        },
        openImg(path) {
            this.img.active = true
            this.img.path = path
        },

    }
};
</script>
