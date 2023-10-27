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
                <img v-if="isImage(file) || isGif(file)" :src="getSrc(file)" alt="Image" />
                <div v-else-if="isTextFile(file)">
                    <i class="bi bi-filetype-txt"></i>
                </div>
                <!-- <span v-else class="unknow-file"><i class="bi bi-file-earmark"></i>{{ truncateText(file.name, 20) }}</span> -->
                <button :disabled="loading" class="cross-delete" @click="removeTempFile(file)">&#10005</button>
            </div>
        </div>

    </div>
    <button :disabled="loading" @click="postCreate(post?.id)" class="btn reply-btn">{{ post?.id ?'Reply' : 'Submit' }}</button>
    <button :disabled="loading" v-if="post?.id" @click="$store.commit('setReplyId', null)" class="btn delete-btn">Cancel</button>
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
        post: null
    },
    data() {
        return {
            tempFiles: [],
            loading: false
        }
    },
    computed: {
        fileInputId() {
            // Генерируйте уникальный идентификатор для каждого компонента
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
    },
    methods: {
        generatePlaceholder() {
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
            const index = this.tempFiles.indexOf(file);
            if (index > -1) {
                this.tempFiles.splice(index, 1);
            }
        },
        getSrc(file) {
            return URL.createObjectURL(file);
        },

        isImage(file) {
            // Список расширений изображений
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

            // Получаем расширение файла (предполагаем, что расширение находится в конце имени файла)
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Проверяем, является ли расширение изображением
            return imageExtensions.includes(fileExtension);
        },

        isGif(file) {
            // Проверяем, является ли файл GIF-изображением
            return this.isImage(file) && file.name.toLowerCase().endsWith('.gif');
        },

        isTextFile(file) {
            // Список расширений текстовых файлов
            const textFileExtensions = ['txt'];

            // Получаем расширение файла
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Проверяем, является ли расширение текстовым файлом
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
                            this.loading = false
                        });
                });
            } else {
                alert('Nothing to submit')
            }

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
