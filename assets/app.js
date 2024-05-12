import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css'
const currentUrl = window.location.href;
const bookingPage = currentUrl.includes("/booking");
const bandPage = currentUrl.includes("/band");
const bandEventPage = currentUrl.match(/\/band\/\d+\/event/);
const bandMemberPage = currentUrl.match(/\/band\/\d+\/members/);
const bandEditPage = currentUrl.match(/\/band\/\d+\/edit/);
const bandInfoPage = currentUrl.match(/\/band\/\d+\/infos/);
const hallPage = currentUrl.includes("/hall");
const hallEventPage = currentUrl.match(/\/hall\/\d+\/event/);
const hallMemberPage = currentUrl.match(/\/hall\/\d+\/members/);
const hallEditPage = currentUrl.match(/\/hall\/\d+\/edit/);
const hallInfoPage = currentUrl.match(/\/hall\/\d+\/infos/);
const registerPage = currentUrl.includes("/register")
const searchPage = currentUrl.match(/\/search+$/)
const bandPageOnly = (bandPage && (!bandEventPage && !bandMemberPage && !bandEditPage && !bandInfoPage));
const hallPageOnly = (hallPage && (!hallEventPage && !hallMemberPage && !hallEditPage && !hallInfoPage));


//****************************\\
//***** PAGE INSCRIPTION *****\\
//****************************\\
if (registerPage) {
    passwordInput.addEventListener('input', function () {
        const password = passwordInput.value;
        const hasLength = password.length >= 8;
        const specialChars = ['!', '@', '#', '$', '%', '^', '&', '-', '_', ':', ';', '"', "'", '?', '*'];
        const hasSpecialChar = password.split('').some(char => specialChars.includes(char));
        const hasUppercase = /[A-Z]/.test(password);
        const lengthRequirement = document.querySelector('#lengthRequirement');
        const specialCharRequirement = document.querySelector('#specialCharRequirement');
        const uppercaseRequirement = document.querySelector('#uppercaseRequirement');
        lengthRequirement.style.color = hasLength ? 'green' : 'red';
        specialCharRequirement.style.color = hasSpecialChar ? 'green' : 'red';
        uppercaseRequirement.style.color = hasUppercase ? 'green' : 'red';

        if (hasLength || hasSpecialChar || hasUppercase) {
            progress.style.width = '33.33%';
            progress.classList.remove('segment-orange', 'segment-green');
            progress.classList.add('segment-yellow');
        }
        if ((hasLength && hasSpecialChar) || (hasLength && hasUppercase) || (hasLength && hasUppercase)) {
            progress.style.width = '66.66%';
            progress.classList.remove('segment-yellow', 'segment-green');
            progress.classList.add('segment-orange');
        }
        if (hasLength && hasSpecialChar && hasUppercase) {
            progress.style.width = '100%';
            progress.classList.remove('segment-yellow', 'segment-orange');
            progress.classList.add('segment-green');
        }
        if (!hasLength && !hasSpecialChar && !hasUppercase) {
            progress.style.width = '0';
            progress.classList.remove('segment-yellow', 'segment-orange', 'segment-green');
        }
    });
}

//************************\\
//***** CALENDRIER *****\\
//************************\\
if (bookingPage || bandPageOnly || hallPageOnly) {
    document.addEventListener('DOMContentLoaded', function () {

        function openEventModal(date, events) {
            const formattedDate = date.replace(/-(\d)-/g, '-0$1-').replace(/-(\d{1})$/g, '-0$1');
            const eventsOnSelectedDate = events.filter(event => event.date === formattedDate);

            if (eventsOnSelectedDate.length > 0) {
                const modalContent = document.querySelector('#eventModal .modal-content');
                modalContent.innerHTML = '<div class="close hidden" onclick="closeModal()">&times;</div>';

                // Utilisez un objet pour regrouper les groupes par date
                const groupedEvents = {};

                // Bouclez sur les événements pour les regrouper par date
                eventsOnSelectedDate.forEach(eventDetails => {
                    if (!groupedEvents[eventDetails.date]) {
                        groupedEvents[eventDetails.date] = [];
                    }
                    // Utilisez eventDetails.bands comme un tableau d'objets pour chaque groupe
                    eventDetails.halls.forEach(hall => {
                        groupedEvents[eventDetails.date].push({
                            name: hall.name,
                            logo: hall.logo,
                            city: hall.city,
                            status: hall.status,
                            // Ajoutez d'autres détails de groupe ici si nécessaire
                        });
                    });
                });

                // Bouclez sur les groupes regroupés et ajoutez-les à la modale
                for (const [date, halls] of Object.entries(groupedEvents)) {
                    modalContent.innerHTML += `
                        <div class="flex justify-between item-align bg-slate-500 relative rounded-t-md text-white p-2 ">
                            <p> ${formatDate(date)}</p>
                            <div class="close ml-3 px-1 font-bold bg-slate-700 rounded-full " onclick="closeModal()">&times;</div>
                        </div>
                        `;
                    halls.forEach(hall => {
                        if (hall.status == "1") {
                            modalContent.innerHTML += `
                                <div class="flex justify-between items-center">
                                    <div class="flex p-2 items-center relative">
                                        <div class="img-project-db border-2 border-green-500 ">
                                            <img src="${hall.logo}" alt="">
                                        </div>
                                        <div class=" bg-green-500 text-white px-2 py-1 rounded-full absolute text-xs top-1 left-10">
                                          ✓
                                        </div>
                                        <div>
                                            <p class="text-blue-400 font-semibold">${hall.name}</p>
                                            <p>${hall.city}</p>
                                        </div>
                                    </div>
                                </div>
                                `;
                        } else if (hall.status == "3") {
                            modalContent.innerHTML += `
                                <div class="flex justify-between items-center">
                                    <div class="flex p-2 items-center relative">
                                        <div class="img-project-db border-2 bg-slate-300">
                                            <img src="${hall.logo}" alt="">
                                        </div>
                                        <div class="bg-slate-300 text-white px-2 py-1 rounded-full absolute text-xs top-1 left-10">
                                            ⧖
                                        </div>
                                        <div>
                                            <p class="text-blue-400 font-semibold">${hall.name}</p>
                                            <p>${hall.city}</p>
                                        </div>
                                    </div>
                                </div>
                                `;
                        }
                    });
                }

                // Affichez la modale
                document.querySelector('#eventModal').style.display = 'block';
            } else { // Aucun événement trouvé pour la date sélectionnée
                console.error(`Aucun détail d'événement trouvé pour la date : ${date}`);
            }
        }

        function openEventModalHall(date) {
            const formattedDate = date.replace(/-(\d)-/g, '-0$1-').replace(/-(\d{1})$/g, '-0$1');
            const eventsOnSelectedDate = events.filter(event => event.date === formattedDate);

            if (eventsOnSelectedDate.length > 0) {
                const modalContent = document.querySelector('#eventModal .modal-content');
                modalContent.innerHTML = '<div class="close hidden" onclick="closeModal()">&times;</div>';

                // Utilisez un objet pour regrouper les groupes par date
                const groupedEvents = {};

                // Bouclez sur les événements pour les regrouper par date
                eventsOnSelectedDate.forEach(eventDetails => {
                    if (!groupedEvents[eventDetails.date]) {
                        groupedEvents[eventDetails.date] = {
                            statusDate: eventDetails.statusDate,
                            bands: []
                        };
                    }
                    // Utilisez eventDetails.bands comme un tableau d'objets pour chaque groupe
                    eventDetails.bands.forEach(band => {
                        groupedEvents[eventDetails.date].bands.push({
                            name: band.name,
                            logo: band.logo,
                            music: band.music,
                            style: band.style,
                            status: band.status
                        });
                    });
                });

                // Bouclez sur les groupes regroupés et ajoutez-les à la modale
                for (const [date, dateDetails] of Object.entries(groupedEvents)) {
                    const { statusDate, bands } = dateDetails;

                    if (statusDate == "1") {
                        modalContent.innerHTML += `
                        <div class="flex justify-between item-align bg-green-500 relative rounded-t-md text-white p-2 ">
                        <p> ${formatDate(date)}</p>
                        <div class="close ml-3 px-1 font-bold bg-green-700 rounded-full " onclick="closeModal()">&times;</div>
                        </div>
                        `;
                        bands.forEach(band => {
                            if (band.status == "validate") {
                                modalContent.innerHTML += `
                                <div class="flex justify-between items-center">
                                    <div class="flex p-2 items-center relative">
                                        <div class="img-project-db border-2 border-green-500 ">
                                            <img src="${band.logo}" alt="">
                                        </div>
                                        <div class=" bg-green-500 text-white px-2 py-1 rounded-full absolute text-xs top-1 left-10">
                                            ✓
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
                    } else {
                        modalContent.innerHTML += `
                            <div class="flex justify-between item-align bg-slate-500 relative rounded-t-md text-white p-2 ">
                            <p> ${formatDate(date)}</p>
                            <div class="close ml-3 px-1 font-bold bg-slate-700 rounded-full " onclick="closeModal()">&times;</div>
                            </div>
                            `;

                        bands.forEach(band => {
                            if (band.status == "validate") {
                                modalContent.innerHTML += `
                                    <div class="flex justify-between items-center">
                                        <div class="flex p-2 items-center relative">
                                            <div class="img-project-db border-2 border-slate-500 ">
                                                <img src="${band.logo}" alt="">
                                            </div>
        
                                            <div>
                                                <p class="text-blue-400 font-semibold">${band.name}</p>
                                                <p>${band.music}/${band.style}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        })
                    }

                }

                // Affichez la modale
                document.querySelector('#eventModal').style.display = 'block';
            } else { // Aucun événement trouvé pour la date sélectionnée
                console.error(`Aucun détail d'événement trouvé pour la date : ${date}`);
            }
        }

        function infoClickEventListener(element, bandEventsWithEventStatus1) {
            element.addEventListener('click', () => {
                console.log(bandEventsWithEventStatus1);
                const formBooking = document.querySelector('#formBooking');
                const confirmation = document.querySelector('#confirmation');
                formBooking.style.display = 'none';
                confirmation.style.display = 'block';

                let bandName = bandEventsWithEventStatus1.bandName;

                // bandEventsWithEventStatus1.forEach((bandEvent, index) => {
                //         bandNames = '<strong>' + bandEvent.bandName + '</strong>';
                //     });

                confirmation.innerHTML = `
                        <div class="p-2 bg-slate-200 border border-slate-700 rounded text-slate-700"><div class="flex"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48" class="mr-2"><g fill="none"><path stroke="#334155" stroke-linejoin="round" stroke-width="4" d="M24 44a19.937 19.937 0 0 0 14.142-5.858A19.937 19.937 0 0 0 44 24a19.938 19.938 0 0 0-5.858-14.142A19.937 19.937 0 0 0 24 4A19.938 19.938 0 0 0 9.858 9.858A19.938 19.938 0 0 0 4 24a19.937 19.937 0 0 0 5.858 14.142A19.938 19.938 0 0 0 24 44Z"/><path fill="#334155" fill-rule="evenodd" d="M24 37a2.5 2.5 0 1 0 0-5a2.5 2.5 0 0 0 0 5" clip-rule="evenodd"/><path stroke="#334155" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M24 12v16"/></g></svg><strong>Attention</strong>,</div> Félicitation 🎉, vous avez déjà un évènement de prévu avec  <strong>${bandName}</strong> pour cette date. <br> Cependant, vous ne pouvez pas réserver des dates ou vous n'êtes pas disponible. Merci de bien vouloir en sélectionner une autre.</div>
                    `;
            });
        }

        function addClickEventListener(element, day, month, year, eventCount, bandEventsWithEventStatus3) {
            element.addEventListener('click', () => {
                const formattedDate = `${day.toString().padStart(2, '0')}-${(month + 1).toString().padStart(2, '0')}-${year}`;
                document.querySelector('#booking_date').value = formattedDate;
        
                // Afficher le nombre d'événements avec le statut 3 au-dessus du formulaire
                const eventCountDisplay = document.querySelector('#eventCountDisplay');
                const formBooking = document.querySelector('#formBooking');
                const confirmation = document.querySelector('#confirmation');
                formBooking.style.display = 'block';
                confirmation.style.display = 'none';

                if (eventCount == 0) {
                    eventCountDisplay.innerHTML = `<span class=" text-green-500">Libre</span>`;
                } else {
                    const demandeText = eventCount < 2 ? 'demande' : 'demandes';
                    eventCountDisplay.innerHTML = `<span class="text-red-500">${eventCount} ${demandeText} en cours</span>`;
                }

        
                if (bandEventsWithEventStatus3.length > 0) {
                    let bandNames = '';

                    bandEventsWithEventStatus3.forEach((bandEvent, index) => {
                        bandNames += '<strong>' + bandEvent.bandName + '</strong>';
                
                        if (index === bandEventsWithEventStatus3.length - 1) {
                            bandNames += '';
                        } else if (index === bandEventsWithEventStatus3.length - 2) {
                            bandNames += ' et ';
                        } else {
                            bandNames += ', ';
                        }
                    });
                    // Masquer le bouton de réservation
                    formBooking.style.display = 'none';
                    confirmation.style.display = 'block';
        
                    // Afficher le message de confirmation
                    confirmation.innerHTML = `
                        <div class="p-2 bg-slate-200 border border-slate-700 rounded text-slate-700"><div class="flex"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48" class="mr-2"><g fill="none"><path stroke="#334155" stroke-linejoin="round" stroke-width="4" d="M24 44a19.937 19.937 0 0 0 14.142-5.858A19.937 19.937 0 0 0 44 24a19.938 19.938 0 0 0-5.858-14.142A19.937 19.937 0 0 0 24 4A19.938 19.938 0 0 0 9.858 9.858A19.938 19.938 0 0 0 4 24a19.937 19.937 0 0 0 5.858 14.142A19.938 19.938 0 0 0 24 44Z"/><path fill="#334155" fill-rule="evenodd" d="M24 37a2.5 2.5 0 1 0 0-5a2.5 2.5 0 0 0 0 5" clip-rule="evenodd"/><path stroke="#334155" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M24 12v16"/></g></svg><strong>Attention</strong>,</div> Vous avez déjà des demandes de réservation pour cette date avec ${bandNames}. Si une salle valide votre réservation, cela annulera les autres demandes <br> Voulez-vous continuer ?</div>
                        <button id="btnConfirm" class="btnFormAccess">C'est compris !</button>
                    `;
        
                    // Ajouter un écouteur d'événements pour le bouton de confirmation
                    const btnConfirm = document.querySelector('#btnConfirm');
                    btnConfirm.addEventListener('click', () => {
                        // Réafficher le bouton de réservation
                        formBooking.style.display = 'block';
                        // Supprimer le message de confirmation
                        confirmation.innerHTML = '';
                    });
                }
            });
        }
        
        const calendar = document.querySelector('#calendrier');
        const prevMonthButton = document.querySelector('#prevMonth');
        const nextMonthButton = document.querySelector('#nextMonth');


        let currentDate = new Date();
        let apiUrl = '';
        if (bookingPage) {
            // Extraire l'ID de la salle de l'URL
            const hallId = currentUrl.split('/').pop();

            // Construire l'URL de l'API
            apiUrl = `/api/booking/${hallId}`;
        }
        if (bandPage) {
            const bandId = currentUrl.split('/').pop();

            // Construire l'URL de l'API
            apiUrl = `/api/band-event/${bandId}`;
        }

        if (hallPage) {
            const hallId = currentUrl.split('/').pop();

            // Construire l'URL de l'API
            apiUrl = `/api/hall-event/${hallId}`;
        }



        let events = [];
        // Effectuer la requête fetch vers l'API
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {

                events = data;
                console.log(events);
                renderCalendar(events);
            })
            .catch(error => console.error('Erreur lors de la récupération des données :', error));

        // Fonction de création du calendrier
        function renderCalendar(events) {
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();


            // On met à jour le mois et l'année
            monthYearHeader.textContent = `${getMonthName(currentMonth)} ${currentYear}`;

            // Effacer le contenue du calendrier précédent
            calendar.innerHTML = '';

            // Création du header du calendrier avec les noms des jours
            const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            dayNames.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'day';
                dayHeader.textContent = day;
                calendar.appendChild(dayHeader);
            });

            // Création des jours du mois
            const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();
            const daysInPreviousMonth = new Date(currentYear, currentMonth, 0).getDate();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            // Calculer la position du premier jour dans la grid
            let startPosition = firstDayOfMonth - 1;

            // Remplir les jours du mois précédent
            for (let day = daysInPreviousMonth - startPosition + 1; day <= daysInPreviousMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'day other-month-day'; // Add a class to style differently
                dayElement.textContent = day;
                calendar.appendChild(dayElement);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.classList.add('day', 'current-month-day');
                if (bookingPage) {
                    dayElement.classList.add('valide-day');
                }
                dayElement.textContent = day;

                if (bookingPage) {

                    // Ajoutez une condition pour vérifier si le jour est dans le passé
                    if (new Date(currentYear, currentMonth, day) < new Date()) {
                        dayElement.classList.add('other-month-day');
                        dayElement.classList.remove('valide-day');
                    } else {

                        // Vérifier s'il y a un évènement pour ce jour
                        const eventsOnDay = events.filter(event => {
                            const eventDate = new Date(event.date);
                            return eventDate.getDate() === day && eventDate.getMonth() === currentMonth && eventDate.getFullYear() === currentYear;
                        });

                        const bandEventsOnDay = events.filter(event => {
                            const bandEventDate = new Date(event.dateBandEvent);
                            return bandEventDate.getDate() === day && bandEventDate.getMonth() === currentMonth && bandEventDate.getFullYear() === currentYear;
                        })



                        // On cherche et on vérifie le statut de la et des dates de ce jour
                        const eventWithStatus1 = eventsOnDay.find(event => event.statusDate === '1');
                        const eventWithStatus2 = eventsOnDay.find(event => event.statusDate === '2');
                        // Utiliser filter pour obtenir tous les événements avec le statut 3
                        const eventWithStatus3 = eventsOnDay.filter(event => event.statusDate === '3');

                        const bandEventsWithEventStatus1 = bandEventsOnDay.find(event => event.eventStatus === 1)
                        const bandEventsWithEventStatus3 = bandEventsOnDay.filter(event => event.eventStatus === 3)

                        if(bandEventsWithEventStatus1 && !eventWithStatus1){
                            dayElement.classList.remove('valide-day');
                            dayElement.classList.add('band-reserved');
                            infoClickEventListener(dayElement, bandEventsWithEventStatus1);
                        }

                        if(bandEventsWithEventStatus3.length > 0){
                            const puce = document.createElement('div');
                            puce.classList.add('puce');
                            dayElement.style.position = 'relative';
                            dayElement.appendChild(puce);
                        }

                        if (eventWithStatus1) {
                            dayElement.classList.remove('valide-day');
                            dayElement.classList.remove('band-reserved');
                            dayElement.classList.remove('puce');

                            dayElement.classList.add('event-reserved');
                        } else if (!bandEventsWithEventStatus1) {
                            if(eventWithStatus3.length > 0){
                            dayElement.classList.remove('valide-day');
                            dayElement.classList.add('in-progress');
                            addClickEventListener(dayElement, day, currentMonth, currentYear, eventWithStatus3.length, bandEventsWithEventStatus3);
                        } else {
                            addClickEventListener(dayElement, day, currentMonth, currentYear, 0, bandEventsWithEventStatus3);
                        }}
                    }
                }
                else {
                    let currentDate = new Date();
                    if (day === currentDate.getDate() && currentMonth === currentDate.getMonth() && currentYear === currentDate.getFullYear()) {
                        dayElement.classList.add('current-day'); // Add a class for the current day
                    }

                    // Check if there are events on this day
                    const eventsOnDay = events.filter(event => {
                        const eventDate = new Date(event.date);
                        return eventDate.getDate() === day && eventDate.getMonth() === currentMonth && eventDate.getFullYear() === currentYear;
                    });

                    if (bandPage) {

                        // Add event styling if there are events
                        if (eventsOnDay.length > 0 && eventsOnDay.every(event => event.halls.every(hall => hall.status !== "2"))) {
                            if (eventsOnDay.every(event => event.halls.every(hall => hall.status == "1"))) {
                                dayElement.classList.add('event-day-valid');
                            } else {
                                dayElement.classList.add('event-day');
                            }
                            dayElement.addEventListener('click', () => openEventModal(`${currentYear}-${currentMonth + 1
                                }-${day}`, events));
                        }
                    } else {
                        if (eventsOnDay.length > 0) {
                            if (eventsOnDay.every(event => event.statusDate == "1")) {
                                dayElement.classList.add('event-day-valid');
                            } else if (eventsOnDay.every(event => event.statusDate == "3")) {
                                dayElement.classList.add('event-day');
                            }
                            dayElement.addEventListener('click', () => openEventModalHall(`${currentYear}-${currentMonth + 1}-${day}`));
                        }

                    }

                }
                calendar.appendChild(dayElement);
            }


            // Remplir les jours restants de la grille avec le mois suivant
            const remainingDays = 35 - startPosition - daysInMonth; // 35 est le nombre max de jour sur la grid 
            for (let day = 1; day <= remainingDays; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'day other-month-day';
                dayElement.textContent = day;
                calendar.appendChild(dayElement);
            }
        }

        // fonction pour avoir le nom du mois affiché
        function getMonthName(month) {
            const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            return monthNames[month];
        }

        // Event listeners pour les boutons de navigation
        prevMonthButton.addEventListener('click', function () {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(events);
        });
        nextMonthButton.addEventListener('click', function () {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(events);
        });



        function formatDate(date) {
            const options = {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            const formattedDate = new Date(date).toLocaleDateString('fr-FR', options);
            return formattedDate;
        }


    });

}


//***********************\\
//***** PAGE SEARCH *****\\
//***********************\\
if (searchPage) {

    const searchGeo = document.querySelector('#searchGeo');
    const searchName = document.querySelector('#searchName');
    const searchMusic = document.querySelector('#searchMusic');
    const searchDate = document.querySelector('#searchDate');

    document.addEventListener('DOMContentLoaded', () => {
        const hallContainer = document.querySelector('#hallContainer');

        const updateDisplayedHalls = () => {
            fetch(`/api/search`)
                .then(response => response.json())
                .then(data => {
                    let filteredHalls = data.filter(hall => {
                        const isNameMatch = hall.name.toLowerCase().includes(searchName.value.toLowerCase());
                        const isGeoMatch = (
                            hall.hallInfo.city.toLowerCase().includes(searchGeo.value.toLowerCase()) ||
                            hall.hallInfo.department.toLowerCase().includes(searchGeo.value.toLowerCase()) ||
                            hall.hallInfo.region.toLowerCase().includes(searchGeo.value.toLowerCase()) ||
                            hall.hallInfo.zipCode.toString().includes(searchGeo.value.toLowerCase())
                        );
                        const isMusicMatch = hall.music_categories.some(category => category.name.toLowerCase().includes(searchMusic.value.toLowerCase()));
                        const isDateMatch = hall.eventListe.includes(searchDate.value);

                        return isNameMatch && isGeoMatch && isMusicMatch && !isDateMatch;
                    });

                    hallContainer.innerHTML = '';

                    filteredHalls.forEach(hall => {
                        const cardResult = document.createElement('div');
                        cardResult.classList.add('card-result');

                        cardResult.innerHTML = `
                             <h4 class="bg-slate-700 text-white p-2 rounded-t-md pl-32 pt-4 pb-1 flex justify-between items-center">
                               <span class="font-bold">${hall.name}</span>
                               <span class="text-sm flex items-center">
                                   ${hall.nbrEvent}
                                   <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewbox="0 0 24 24" class="ml-1"><path fill="white" d="M14.5 18q-1.05 0-1.775-.725T12 15.5q0-1.05.725-1.775T14.5 13q1.05 0 1.775.725T17 15.5q0 1.05-.725 1.775T14.5 18M5 22q-.825 0-1.412-.587T3 20V6q0-.825.588-1.412T5 4h1V2h2v2h8V2h2v2h1q.825 0 1.413.588T21 6v14q0 .825-.587 1.413T19 22zm0-2h14V10H5z"/></svg>
                               </span>
                             </h4>
                             <div class="img-hall-search">
                               <img src="${hall.logo}" alt="${hall.name}">
                             </div>

                           <div class="pl-32 flex justify-between">
                             <div>
                               <p class="flex items-center py-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 256 256" class="mr-2">
                                   <path fill="currentColor" d="M128 16a88.1 88.1 0 0 0-88 88c0 75.3 80 132.17 83.41 134.55a8 8 0 0 0 9.18 0C136 236.17 216 179.3 216 104a88.1 88.1 0 0 0-88-88m0 56a32 32 0 1 1-32 32a32 32 0 0 1 32-32"/>
                                 </svg>
                                 ${hall.hallInfo.city} ${hall.hallInfo.zipCode !== "" ? `(${hall.hallInfo.zipCode})` : ""}
                               </p>
                               <p class="flex items-center py-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="mr-2">
                                   <path fill="currentColor" d="M6.27 2.289a2.936 2.936 0 0 0-2.159 2.035A37.876 37.876 0 0 0 4.1 9.842c.045 0 .491-.086.992-.188L6 9.467l.023-2.334c.027-2.711.016-2.652.584-2.938L6.8 4.1H12c5.737 0 5.316-.019 5.64.256c.324.275.308.2.334 2.776L18 9.467l.91.187c.5.1.947.187.992.188a37.876 37.876 0 0 0-.01-5.518a2.927 2.927 0 0 0-2.177-2.037c-.551-.132-10.9-.13-11.442 0m2.515 4.2l-.466 1.862L8 9.637l-.507.475l-.507.474l-2.486.465l-2.485.466v3.754A31.9 31.9 0 0 0 2.071 19l2.415-.9c1.3-.487 2.4-.893 2.45-.9c.069-.014.22.1.577.438l.487.45l.322 1.283l.466 1.861l.145.579h6.14l.145-.579l.466-1.861l.316-1.283l.484-.455c.357-.337.508-.452.577-.438c.051.009 1.154.415 2.45.9l2.418.905a31.9 31.9 0 0 0 .058-3.732v-3.751l-2.487-.466l-2.485-.465l-.507-.474L16 9.637l-.322-1.283l-.466-1.862l-.145-.579H8.93l-.145.579m4.2 5.182c2.541.608 3.471 3.2 1.83 5.091c-2.094 2.419-6.41 1.1-6.428-1.964c-.012-2.149 2.253-3.689 4.6-3.127"/>
                                 </svg>
                                 ${hall.structure !== "" ? `(${hall.structure})` : "Non précisé"}
                               </p>
                             </div>
                             <div class="text-right p-1 text-sm">
                               <p>Réponse</p>
                               <p>${hall.rateResp}%</p>
                             </div>
                           </div>
                           <div class="list-category flex p-3 mb-6 justify-evenly">
                             ${hall.displayedBands.map(band => `<p class="text-green-500 mx-2">${band}✓</p>`).join('')}
                           </div>
                           <a href="search/booking/${hall.id}" class="reserveLink">
                           <h4 class="bg-slate-400 hover:bg-teal-500 text-white rounded-b-md text-center py-1 absolute bottom-0 w-full">Réserver</h4>
                               </a>
                         `;

                        hallContainer.appendChild(cardResult);
                    });
                })
                .catch(error => console.error('Error fetching halls:', error));
        };

        searchName.addEventListener('input', updateDisplayedHalls);
        searchGeo.addEventListener('input', updateDisplayedHalls);
        searchMusic.addEventListener('change', updateDisplayedHalls);
        searchDate.addEventListener('input', updateDisplayedHalls);


        updateDisplayedHalls();
    });
}

//*******************************\\
//***** PAGE HALL/BAND INFO *****\\
//*****       API GEO       *****\\
//*******************************\\
if (hallInfoPage || bandInfoPage) {
    document.addEventListener('DOMContentLoaded', function () {
        let page = "";
        if (hallInfoPage) {
            page = "hall"
        } else {
            page = "band"
        }
        const apiUrl = 'https://geo.api.gouv.fr/communes?codePostal=';
        const format = '&format=json';
        const regionApiUrl = 'https://geo.api.gouv.fr/regions/';
        const departementApiUrl = 'https://geo.api.gouv.fr/departements/';
        const zipCode = document.querySelector(`#${page}_info_zipCode`);
        const cityOption = document.querySelector(`#cityOption`);
        const city = document.querySelector(`#${page}_info_city`);
        const region = document.querySelector(`#${page}_info_region`);
        const departement = document.querySelector(`#${page}_info_department`);
        const country = document.querySelector(`#${page}_info_country`);
        const zipCodeWarning = document.querySelector('#zipCodeWarning');

        let defaultCity = city.value;
        if (defaultCity) {
            // Définissez la valeur du sélecteur sur la valeur par défaut
            cityOption.innerHTML += '<option value="' + defaultCity + '" selected >' + defaultCity + '</option>';
        }

        cityOption.addEventListener('change', function () {
            // Mettez à jour la valeur du champ 'form.city' avec la valeur sélectionnée dans 'cityOption'
            let selectedCity = this.value;
            city.value = selectedCity;
        });

        zipCode.addEventListener('blur', function () {
            let code = this.value;

            if (!code) {
                // Afficher le message d'avertissement si le code postal est vide
                zipCodeWarning.textContent = 'Saisissez en premier le code postal';
                return;
            } else {
                // Cacher le message d'avertissement si le code postal est rempli
                zipCodeWarning.textContent = '';
            }

            let url = apiUrl + code + format;

            fetch(url)
                .then(response => response.json())
                .then(results => {
                    if (results.length) {
                        let regionCode = results[0].codeRegion;
                        let regionUrl = regionApiUrl + regionCode;

                        fetch(regionUrl)
                            .then(response => response.json())
                            .then(regionData => {
                                if (regionData.nom) {
                                    region.value = regionData.nom;
                                    country.value = 'France';
                                }
                            })
                            .catch(err => {
                                console.log(err);
                            });

                        let departementCode = results[0].codeDepartement;
                        let departementUrl = departementApiUrl + departementCode;

                        fetch(departementUrl)
                            .then(response => response.json())
                            .then(departementData => {
                                if (departementData.nom) {
                                    departement.value = departementData.nom;
                                }
                            })
                            .catch(err => {
                                console.log(err);
                            });

                        cityOption.innerHTML = '<option value="" disabled selected>Sélectionnez une ville</option>';
                        results.forEach(function (value) {
                            cityOption.innerHTML += '<option value="' + value.nom + '">' + value.nom + '</option>';
                        });

                    }
                })
                .catch(err => {
                    console.log(err);
                });
        });
    });

}


//*******************************\\
//***** PAGE BAND/HALL SHOW *****\\
//*******************************\\
if (bandPageOnly || hallPageOnly) {
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.querySelector('.event-confirm-db-container');
        const content = document.querySelector('.event-confirm-db-content');
        const arrowLeft = document.querySelector('#arrow-left');
        const arrowRight = document.querySelector('#arrow-right');
        const currentUrl = window.location.href;
        let count = 0;

        let apiUrl = '';

        if (bandPageOnly) {
            const bandId = currentUrl.split('/').pop();

            // Construire l'URL de l'API
            apiUrl = `/api/band-event/${bandId}`;

            let events = [];

            // Effectuer la requête fetch vers l'API
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    events = data;
                    events.forEach(e => {
                        const eventDate = new Date(e.date);
                        const currentDate = new Date();
                        if (e.halls.length > 0 && e.halls[0].status == 1 && eventDate >= currentDate) {
                            count++;
                        }
                    });
                    updatePosition();
                })
                .catch(error => console.error('Erreur lors de la récupération des données :', error));

        } else {
            const hallId = currentUrl.split('/').pop();

            // Construire l'URL de l'API
            apiUrl = `/api/hall-event/${hallId}`;

            let events = [];

            // Effectuer la requête fetch vers l'API
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    events = data;
                    events.forEach(e => {
                        const eventDate = new Date(e.date);
                        const currentDate = new Date();
                        if (e.statusDate == 1 && eventDate >= currentDate) {
                            count++;
                        }
                    });

                    updatePosition();
                })
                .catch(error => console.error('Erreur lors de la récupération des données :', error));
        }

        // Initialisation de la position à 0
        let currentPosition = 0;

        arrowLeft.addEventListener('click', function () { // Décrémente la position
            currentPosition -= 100;
            updatePosition();
        });

        arrowRight.addEventListener('click', function () { // Incrémente la position
            currentPosition += 100;
            updatePosition();
        });

        arrowLeft.addEventListener('mouseover', function () { // Change le curseur au survol
            arrowLeft.style.cursor = 'pointer';
        });

        arrowRight.addEventListener('mouseover', function () { // Change le curseur au survol
            arrowRight.style.cursor = 'pointer';
        });

        arrowLeft.addEventListener('mouseout', function () { // Rétablit le curseur par défaut
            arrowLeft.style.cursor = 'default';
        });

        arrowRight.addEventListener('mouseout', function () { // Rétablit le curseur par défaut
            arrowRight.style.cursor = 'default';
        });

        function updatePosition() { // Limite la position pour éviter de dépasser le contenu
            currentPosition = Math.max(0, Math.min(currentPosition, content.scrollWidth - container.clientWidth));

            // Ajout d'une transition pour une animation fluide
            content.style.transition = 'left 0.5s ease-in-out';
            content.style.left = `-${currentPosition}%`;

            // Rend la flèche gauche transparente et non cliquable si à la limite gauche
            arrowLeft.style.opacity = currentPosition === 0 ? 0.5 : 1;
            arrowLeft.style.pointerEvents = currentPosition === 0 ? 'none' : 'auto';

            // Rend la flèche droite transparente et non cliquable si à la limite droite
            arrowRight.style.opacity = currentPosition >= (count - 1) * 100 ? 0.5 : 1;
            arrowRight.style.pointerEvents = currentPosition >= (count - 1) * 100 ? 'none' : 'auto';

            // Réinitialise la transition après l'animation
            setTimeout(() => {
                content.style.transition = '';
            }, 500);
        }

    });
}


