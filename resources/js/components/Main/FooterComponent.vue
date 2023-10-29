<template>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div>
                <div class="company-info">
                    <h4>Mega Forum</h4>
                    <p>&copy; 2023. All rights reserved</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="support-info">
                <h4>Technical support</h4>
                <div>
                    <p>
                        Contact us by mail:
                        <a href="mailto:illya.gerasimets@gmail.com">illya.gerasimets@gmail.com</a>
                    </p>
                </div>
                <div class="social-icons">
                    <p>Or through social networks:</p>
                    <div>
                        <a target="_blank" href="https://t.me/h_elijah"><i class="bi bi-telegram"></i></a>
                        <a target="_blank" href="https://instagram.com/h._elijah_?igshid=OGQ5ZDc2ODk2ZA%3D%3D&utm_source=qr"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</template>

<script>
export default {
    mounted() {},
};
</script>

<style lang="scss" scoped>
footer {
    background-color: #f2f9ff;
    color: #005580;
    width: 100%;
    padding: 20px 0;
    text-align: center;
    font-size: 14px;
    margin-top: auto;
    position: relative;
    bottom: 0;
}

.row {
    padding: 20px;
}

.social-icons {
    i {
        font-size: 25px;
    }
}

.container {
    justify-content: space-around;
    flex-wrap: wrap;
}

.footer p {
    margin: 0;
}

.company-info h4,
.support-info h4 {
    color: #005580;
    font-size: 18px;
    font-weight: bold;
}

.company-info p {
    margin-bottom: 0;
}

.support-info p {
    margin-bottom: 8px;
}

.support-info a {
    color: #005580;
    text-decoration: none;
}

.support-info a:hover {
    text-decoration: underline;
}


.social-icons a {
    color: #005580;
    margin-right: 10px;
}

.social-icons a:last-child {
    margin-right: 0;
}

.copy-info p {
    margin-bottom: 0;
}

@media screen and (max-width: 768px) {

    .support-info-content,
    .social-icons {
        display: block;
        margin: 0;
    }

    .social-icons {
        margin-top: 10px;
    }
}
</style>
