/* Comment reply */
document.querySelectorAll("[data-reply]").forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault()

        // on assigne l'id du commentaire à la réponse
        const parentId = document.getElementById('comment_parent_id')
        parentId.value = this.dataset.id

        // On déplace le formulaire en dessous de commentaire
        const form = document.getElementById('commentForm')
        const cloneForm = form.cloneNode(true)
        cloneForm.hidden = true
        btn.after(cloneForm)
        cloneForm.hidden = false

        // On crée le bouton annuler du clone du formulaire
        document.querySelectorAll('.btn_cancel').forEach(btnCancel => {
            btnCancel.addEventListener('click', function () {
                cloneForm.hidden = true
            })
        })

        console.log()
    })
})