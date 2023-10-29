<template>
<div class="user-cabinet">
    <div class="user-avatar" @click="openImageUploader">
        <img v-if="user.avatar" :src="'/storage/'+user.avatar" alt="User Avatar" />
        <img v-else src="/images/default-avatar.png" alt="" />
    </div>
    <h1 class="cabinet-title">Your Cabinet</h1>
    <div class="user-info">
        <div class="user-property">Name:</div>
        <div class="user-value">{{ user.name }}</div>
        <div class="user-property">Email:</div>
        <div class="user-value">{{ user.email }}</div>
        <div class="user-property">Created At:</div>
        <div class="user-value">{{ formatDate(user.created_at) }}</div>
    </div>
    <input type="file" ref="fileInput" style="display: none" @change="handleImageChange" accept="image/*" />
    <div :class="{active: showImageUploadModal}" class="avatar-container">
        <div class="view-avatar">
            <img :class="{active: showImageUploadModal}" :src="selectedImage" alt="" @click="selectedImage = false">
        </div>
        <div :class="{active: showImageUploadModal}" class="image-upload-controls">
            <button @click="cancelImageUpload">Cancel</button>
            <button @click="uploadImage">Download</button>
        </div>
    </div>
    <div class="avatar-background" :class="{active: showImageUploadModal}"></div>
</div>
</template>

<script>
export default {
    data() {
        return {
            selectedImage: null,
            imageFile: null,
            showImageUploadModal: false,
        };
    },
    mounted() {
        this.$store.dispatch('fetchUser');

    },
    computed: {
        user() {
            return this.$store.getters.getUser
        }
    },
    methods: {
        formatDate(isoDate) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(isoDate).toLocaleDateString('ru-RU', options);
        },
        openImageUploader() {
            this.$refs.fileInput.click();
        },

        handleImageChange(event) {

            this.$store.commit('setLoading', true)
            const file = event.target.files[0];
            if (file) {
                const image = new Image();
                image.src = URL.createObjectURL(file);

                const validateImage = new Promise((resolve, reject) => {
                    image.onload = function () {
                        const width = this.width;
                        const height = this.height;

                        if (width > 1000 || height > 1000) {
                            reject('Max 1000x1000 px');
                        } else {
                            resolve(file);
                        }
                    };
                });

                validateImage
                    .then(validFile => {
                        this.imageFile = validFile;
                        this.selectedImage = URL.createObjectURL(validFile);
                        this.showImageUploadModal = true;
                        this.$store.commit('setLoading', false)
                    })
                    .catch(error => {
                        this.$store.commit('setLoading', false)
                        alert('Max 1000x1000 px');
                    });
            }
        },

        cancelImageUpload() {
            this.imageFile = null
            this.selectedImage = null;
            this.showImageUploadModal = false;
            this.$refs.fileInput.value = null;
        },

        uploadImage() {
            this.$store.commit('setLoading', true)
            let fd = new FormData()

            fd.append('user_id', this.user.id)
            fd.append('avatar', this.imageFile)

            axios.get("/sanctum/csrf-cookie").then((response) => {
                axios
                    .post("/api/avatar", fd)
                    .then((res) => {
                        this.$store.dispatch('fetchUser');
                        this.cancelImageUpload()
                        this.$store.commit('setLoading', false)
                    })
                    .catch((err) => {
                        console.log(err);
                        this.$store.commit('setLoading', false)
                    });
            });
        },
    }
}
</script>

<style lang="scss">
.user-cabinet {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    background-color: #fff;

    .cabinet-title {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-property {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .user-value {
        margin-bottom: 10px;
    }
}

.user-cabinet {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    background-color: #fff;
    text-align: center;

    .user-avatar img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: block;
    }

    .cabinet-title {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .user-info {
        text-align: left;
    }

    .user-property {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .user-value {
        margin-bottom: 10px;
    }

    .avatar-container {
        position: fixed;
        width: 320px;
        height: 460px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        z-index: 9;
        transition: all ease .5s;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);

        opacity: 0;
        visibility: hidden;
    }

    .avatar-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 2;
        opacity: 0;
        transition: opacity 0.3s;
        cursor: pointer;
        transition: all ease .5s;

        opacity: 0;
        visibility: hidden;
    }

    .avatar-container img {

        opacity: 0;
        visibility: hidden;
    }

    /* Анимация появления фона */
    .avatar-container.active,
    .avatar-background.active,
    img.active {
        opacity: 1;
        visibility: visible;
        margin: 0;
    }

    .avatar-background.active {
        opacity: 0.3;
    }

    .view-avatar {
        width: 320px;
        height: 320px;
        overflow: hidden;
        position: relative;
        background: whitesmoke;
        border: 10px whitesmoke solid;
        padding: 10px;
        border-radius: 30px;

        img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }
    }

    .image-upload-controls {
        position: absolute;
        bottom: -30px;
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        text-align: center;
    }

    .image-upload-controls button {
        background-color: #007BFF;
        color: #fff;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        cursor: pointer;
        border-radius: 3px;
        font-size: 16px;
    }

    .image-upload-controls button:hover {
        background-color: #0056b3;
    }

    .image-upload-controls button:focus {
        outline: none;
    }

}
</style>
