document.addEventListener('DOMContentLoaded', function() {
    const favoriteButtons = document.querySelectorAll('.favorite-button');

    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            const url = `/favorite/${type}/${id}`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    this.textContent = 'Retirer des favoris';
                    this.classList.add('bg-red-500', 'hover:bg-red-700');
                    this.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                } else if (data.status === 'removed') {
                    this.textContent = 'Ajouter aux favoris';
                    this.classList.add('bg-blue-500', 'hover:bg-blue-700');
                    this.classList.remove('bg-red-500', 'hover:bg-red-700');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
            });
        });
    });
}); 