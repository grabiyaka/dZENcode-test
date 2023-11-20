<template>
<div class="underline" v-if="$store.state.token.token">
    <div ref="quillEditor"></div>
    <i class="bi bi-info-circle"></i><span>The image should be 320 by 240 pixels, and when attempting to upload an image larger in size, it is automatically resized.</span>
    <div class="files-container">

        <input :id="fileInputId" type="file" @change="handleFileUpload" accept=".jpg, .jpeg, .gif, .png, .txt">
        <label class="file-upload-button file-element" :for="fileInputId"></label>

        <div class="file-element" v-for="file in tempFiles" :key="file.name">
            <span v-if="isTextFile(file)">{{ file.name }}</span>
            <div>
                <img v-if="isImage(file) || isGif(file)" :src="file.path ? '/storage/' + file.path : getSrc(file)" alt="Image" />
                <div v-else-if="isTextFile(file)">
                    <i class="bi bi-filetype-txt"></i>
                </div>
                <!-- <span v-else class="unknow-file"><i class="bi bi-file-earmark"></i>{{ truncateText(file.name, 20) }}</span> -->
                <button :disabled="loading" class="cross-delete" @click="removeTempFile(file)">&#10005</button>
            </div>
        </div>

    </div>
    <button :disabled="loading" @click="submit(post?.id)" class="btn reply-btn">{{ method }}</button>
    <button :disabled="loading" v-if="post?.id" @click="$store.commit('setReplyId', null), $store.commit('setChangeId', null)" class="btn delete-btn">Cancel</button>
</div>
</template>

<script>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import TextViewer from "./TextViewer.vue";

export default {
    components: {
        TextViewer
    },
    props: {
        value: String,
        post: null,
        method: String
    },
    data() {
        return {
            tempFiles: [],
            loading: false
        }
    },
    computed: {
        fileInputId() {
            return `fileInput-${this.post?.id}`;
        },
    },
    mounted() {
        this.quill = new Quill(this.$refs.quillEditor, {
            theme: 'snow',
            placeholder: this.generatePlaceholder()
        });

        this.quill.on('text-change', () => {
            this.$emit('input', this.quill.root.innerHTML);
        });
        if(this.value) this.quill.root.innerHTML = this.value
        
        if(this.post?.files.length){
            this.tempFiles = this.post.files 
        }
    },
    methods: {
        submit(id) {
            if (this.method == 'Create' || this.method == 'Reply') this.postCreate(id)
            else if (this.method == 'Change') this.postUpdate(id)
        },
        generatePlaceholder() {
            if (this.method == 'change') {
                return 'Your changes...'
            }
            const placeholders = [
                "Your idea here...",
                "What's on your mind?",
                "Write down your thoughts...",
                "Your text here...",
                "Share your opinion...",
                "Start typing...",
                "Get inspired and write...",
                "Share your story...",
                "Record your idea...",
                "Interesting text here...",
            ];
            const randomIndex = Math.floor(Math.random() * placeholders.length);
            return placeholders[randomIndex];
        },
        handleFileUpload(event) {
            const selectedFiles = event.target.files;
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'txt'];

            for (let i = 0; i < selectedFiles.length; i++) {
                const file = selectedFiles[i];
                const fileNameParts = file.name.split('.');
                const fileExtension = fileNameParts[fileNameParts.length - 1].toLowerCase();

                if (allowedExtensions.includes(fileExtension)) {
                    this.tempFiles = this.tempFiles.concat(file);
                } else {
                    alert(`File ${file.name} has an invalid extension`);
                }
            }
        },

        removeTempFile(file) {
            if(file.path){
                this.$parent.deleteFile(file, this.post.id)
            }
            const index = this.tempFiles.indexOf(file);
            if (index > -1) {
                this.tempFiles.splice(index, 1);
            }
        },
        getSrc(file) {
            return URL.createObjectURL(file);
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
        postCreate(postId = null) {
            if (this.quill.root.innerHTML != '<p><br></p>' || this.tempFiles.length) {
                this.$store.commit('setLoading', true)
                this.loading = true
                let fd = new FormData()
                const post = {
                    content: ''
                }

                if (postId) {
                    post.content = this.quill.root.innerHTML;
                    post.parent_id = postId

                    this.tempFiles.forEach((file, index) => {
                        fd.append(`files[${index}]`, file);
                    });
                } else {
                    post.content = this.quill.root.innerHTML;

                    this.tempFiles.forEach((file, index) => {
                        fd.append(`files[${index}]`, file);
                    });
                };

                for (let key in post) {
                    if (post.hasOwnProperty(key)) {
                        fd.append(key, post[key]);
                    }
                }
                axios.get("/sanctum/csrf-cookie").then((response) => {
                    axios
                        .post("/api/post", fd)
                        .then((res) => {
                            const result = []
                            if (postId) {
                                this.$store.commit('addPostToParent', {
                                    parentId: postId,
                                    newPost: res.data
                                });
                            } else {
                                this.$store.commit('addPost', res.data);
                            }
                            this.quill.root.innerHTML = '<p><br></p>'
                            this.tempFiles = []
                            this.loading = false
                            this.$store.commit('setLoading', false)
                        })
                        .catch((err) => {
                            this.$store.commit('setLoading', false)
                            alert('error')
                            console.log(err);
                            this.loading = false
                        });
                });
            } else {
                alert('Nothing to submit')
            }

        },
        postUpdate(id) {
            this.$store.commit('setLoading', true)
            this.loading = true
            let fd = new FormData()
            let post = {
                change: this.quill.root.innerHTML
            }

            this.tempFiles.forEach((file, index) => {
                fd.append(`files[${index}]`, file);
            });
            console.log('change');
            axios.get("/sanctum/csrf-cookie").then((response) => {
                axios
                    .patch(`/api/post/${id}`, post)
                    .then((res) => {
                        this.$store.commit('setLoading', false)
                        this.loading = false
                        this.$store.commit('setReplyId', null) 
                        this.$store.commit('setChangeId', null)
                        this.$store.commit('updatePost', res.data)
                        console.log(res.data);
                        alert('saved!');
                    })
                    .catch((err) => {
                        this.$store.commit('setLoading', false)
                        this.loading = false
                        console.log(err);
                    });
            });
        },
        truncateText(text, maxLength) {
            if (text.length > maxLength) {
                const halfLength = Math.floor(maxLength / 2) - 2;
                const beginning = text.slice(0, halfLength);
                const end = text.slice(-halfLength);
                return beginning + '...' + end;
            }
            return text;
        },
    },
    watch: {
        value(newVal) {
            console.log(111);
            this.quill.root.innerHTML = newVal;
        }
    },
};
</script>

<style lang="scss">
.unknow-file {
    display: flexbox;

    i {
        display: block;
        font-size: 150px;
    }
}
</style>
