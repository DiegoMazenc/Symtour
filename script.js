function openEventModal(date) {
    const formattedDate = date.replace(/-(\d)-/g, '-0$1-').replace(/-(\d{1})$/g, '-0$1');
    const eventsOnSelectedDate = events.filter(event => event.date === formattedDate);

    if (eventsOnSelectedDate.length > 0) {
        const modalContent = document.querySelector('#eventModal .modal-content');
        modalContent.innerHTML = '<span class="close" onclick="closeModal()">&times;</span>';
        
        // Utilisez un objet pour regrouper les groupes par date
        const groupedEvents = {};

        // Bouclez sur les événements pour les regrouper par date
       eventsOnSelectedDate.forEach(eventDetails => {
    if (!groupedEvents[eventDetails.date]) {
        groupedEvents[eventDetails.date] = [];
    }
    // Utilisez eventDetails.bands comme un tableau d'objets pour chaque groupe
			eventDetails.bands.forEach(band => {
				groupedEvents[eventDetails.date].push({
					name: band.name,
					logo: band.logo,
					music: band.music,
					style: band.style,
					status: band.status,
					// Ajoutez d'autres détails de groupe ici si nécessaire
				});
			});
		});

		// ...

		// Bouclez sur les groupes regroupés et ajoutez-les à la modale
		for (const [date, bands] of Object.entries(groupedEvents)) {
    modalContent.innerHTML += `<p>Date: ${date}</p>`;
    bands.forEach(band => {
        if (band.status == "validate") {
        modalContent.innerHTML += `
            <div class="flex justify-between items-center">
                <div class="flex p-2 items-center relative">
       
                <div class="img-project-db border-2 bg-slate-300">
                    <img src="${band.logo}" alt="">
                </div>
                <div class="bg-slate-300 text-white px-2 py-1 rounded-full absolute text-xs top-1 left-10">
                    ⧖
                </div>
            <div>
                    <p class="text-blue-400 font-semibold">${band.name}</p>
                    <p>${band.music}/${band.style}</p>
                </div>
            </div>
        </div>
        `;
        } else if (band.status == "guest") {
			 modalContent.innerHTML += `
            <div class="flex justify-between items-center">
                <div class="flex p-2 items-center relative">
      
                <div class="img-project-db border-2 bg-slate-300">
                    <img src="${band.logo}" alt="">
                </div>
                <div class="bg-slate-300 text-white px-2 py-1 rounded-full absolute text-xs top-1 left-10">
                    ⧖
                </div>
        
                <div>
                    <p class="text-blue-400 font-semibold">${band.name}</p>
                    <p>${band.music}/${band.style}</p>
                </div>
            </div>
        </div>
        `;
}
    });

        // Affichez la modale
        document.getElementById('eventModal').style.display = 'block';
    } else {
        // Aucun événement trouvé pour la date sélectionnée
        console.error(`Aucun détail d'événement trouvé pour la date : ${date}`);
    }
}