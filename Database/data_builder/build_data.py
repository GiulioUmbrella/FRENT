from sys import exit, argv
import csv
import random


user_data = []
annunci_data = []
prenotazioni = []
commenti = []
data_base = "2020-02-"

def write_on_csv(header, csv_data, file_path="data.csv"):
    with open(file_path, 'w') as csv_file:
        writer = csv.writer(csv_file)
        writer.writerow(header)
        writer.writerows(csv_data)


def generate_users():
    #15 Nomi
    names = ["Jolanda","Melchiorre","Cirino","Ignazio","Elio","Gianni","Amore","Marisa","Dania","Alberico","Lodovico","Marino","Nella","Pompeo","Alfonso"]
    surnames = ["Rossi","Ferrari","Russo","Bianchi","Romano","Gallo","Costa","Fontana","Conti","Esposito","Ricci","Bruno","Rizzo","Moretti","Marino"]
    default_image = "defaultImages/imgProfiloDefault.png"
    #Need more users? decomment this lines
    '''
    exponential = 15 #Up to len(names)
    if exponential > len(names):
        print("Aggiungi nomi e cognomi per creare più combinazioni")
        exit(-1)

    names_ = []
    for n in names:
        for _ in range(exponential):
            names_.append(n)
    names = names_
    print("names regen")
    surnames_ = []
    for i in range(exponential):
        for s in surnames:
            surnames_.append(s)
    surnames = surnames_
    '''

    emails = ["{}.{}@mail.it".format(name.lower(), surname.lower()) for name, surname in zip(names, surnames)]
    usernames = ["{}.{}".format(name.lower(), surname.lower()) for name, surname in zip(names, surnames)]
    passwords = ["password" for _ in names]
    date_di_nascita = ["1998-01-01" for _ in names]
    telefoni = ["+390000000000" for _ in names]

    for i in range(len(names)):
        user_data.append([str(i+1), names[i], surnames[i], usernames[i], emails[i], passwords[i], date_di_nascita[i], default_image, telefoni[i]])

    user_header = ["id_utente", "nome", "cognome", "mail", "user_name", "password", "data_nascita", "img_profilo", "telefono"]
    write_on_csv(user_header, user_data, "utenti.csv")
    #print(csv_data)

def generate_annunci():
    #numero_annunci = 10
    titoli = ["Casa {}".format(n) for n in ["Loreto","Agrippa","Celeste","Aquila","Amore"]]
    titoli += ["Appartamento {}". format(n) for n in ["Aran","Flann","Glaucia","Nollaig","Amore"]]

    descrizioni = ["Accogliente casa in riva al mare con una vista fantastica. Molto vicina al centro città e ai negozzi dove è possibile fare shopping.",
                    "Casa in aperta campagna, ottimo luogo dove riposarsi nei fine settiman. Consigliata soprattutto alle coppie",
                    "Casa in montagna a 2 metri dalle piste da sci. Otiima per passare la settimana bianca.",
                    "Casa accogliente in centro città a due passi dalla metro. Molto vicina al nuovisissimo MCDonald.",
                    "Casa Amore, un nome una garanzia. Il vostro soggiorno sarà molto appagante.",
                    "Appartamentino in centro città a due metri dalla stazione dei treni e dall'aereoporto.",
                    "Un appartamentino molto accogliente nel quale sono ben accetti animali di tutte le taglie.",
                    "Appartamento dalle nobili origini ristrutturato per renderlo un posticino accogliente e pragmatico.",
                    "Appartemento vista mare con spiaggia annesse, venite da noi a godervi delle bellissime giornate di solo.",
                    "Appartamento dove l'amore è di casa."]
    foto = ["../uploads/defaultImages/c1.jpg",
            "../uploads/defaultImages/c2.jpg",
            "../uploads/defaultImages/c3.jpg",
            "../uploads/defaultImages/c4.jpg",
            "../uploads/defaultImages/c5.jpg",
            "../uploads/defaultImages/a1.jpg",
            "../uploads/defaultImages/a2.jpg",
            "../uploads/defaultImages/a3.jpg",
            "../uploads/defaultImages/a4.jpg",
            "../uploads/defaultImages/a5.jpg",]

    descrizione_foto = ["Stanza con un tavolo, due finestre, separate da una colonna",
                        "Sala da pranzo con zona cucina e zona soggiorno. Una televisione a destra e una porta in fondo a sinistra.",
                        "Stanza con un divano a due posti, uno specchio e  una piccola tv.",
                        "Casa a due piani con giardino.",
                        "Casa ad un unico piano con un garage ed un ampio giardino ben curato.",
                        "Vista esterna di una casa a due piani con un garage e un giardino ben curato.",
                        "Sala da pranzo con un tavolino, 4 sedie, un divanetto e una televisione sopra un mobile.",
                        "Sala da pranzo con cucina, un tavolo con una panca e due sedie. A destra è presente un corridoio.",
                        "Cucina con un tavolo ed un divano sttaccato alla parete. Sulla sinistra c'è una porta finestra.",
                        "Appartamento visto da fuori con un balconcino con delle fioriere. Appartamento a due piani.",
                        "Sala da pranza con salotto, molto grande, pavimento in parquet, tavolo con panca e divano con tavolino."]

    if len(argv) > 1:
        if argv[1] == "--less-random":
            with open("vie.csv", 'r') as vie_file:
                random.seed(42)
                readCSV = [row for row in csv.reader(vie_file, delimiter=',')]
                indirizzi = [str(readCSV[random.randrange(1, len(readCSV))][2]) for _ in titoli]
                citta = ["roma" for _ in titoli]
            hosts = [str(user_data[random.randrange(0, len(user_data)-1)][0]) for _ in titoli]
            stato_approvazione = ["1" for _ in titoli]
            max_ospiti = [str(random.randrange(1, 7)) for _ in titoli]
            prezzo_notte = [str(random.randrange(25, 150)) for _ in titoli]
    else:
        with open("vie.csv", 'r') as vie_file:
            random.seed(42)
            readCSV = [row for row in csv.reader(vie_file, delimiter=',')]
            indirizzi = [str(readCSV[random.randrange(1, len(readCSV))][2]) for _ in titoli]
            citta = [str(readCSV[random.randrange(1, len(readCSV))][1]) for _ in titoli]
        hosts = [str(user_data[random.randrange(0, len(user_data)-1)][0]) for _ in titoli]
        stato_approvazione = [str(random.randrange(0, 2)) for _ in titoli]
        max_ospiti = [str(random.randrange(1, 7)) for _ in titoli]
        prezzo_notte = [str(random.randrange(25, 150)) for _ in titoli]

    for i in range(len(titoli)):
        annunci_data.append([
            str(i+1),
            titoli[i],
            descrizioni[i],
            foto[i],
            descrizione_foto[i],
            indirizzi[i],
            citta[i],
            hosts[i],
            stato_approvazione[i],
            max_ospiti[i],
            prezzo_notte[i],
        ])

    annunci_header = ["id_annuncio", "titolo", "descrizione", "img_anteprima", "desc_anteprima", "indirizzo", "citta", "host", "stato_approvazione", "max_ospiti", "prezzo_notte"]
    write_on_csv(annunci_header, annunci_data, "annunci.csv")


def generate_prenotazioni():

    prenotazioni_header = ["id_prenotazione", "utente", "annuncio", "num_ospiti", "data_inizio", "data_fine"]
    prenotazioni.append(["1", "1", "1", "1", data_base+"2", data_base+"7"])
    prenotazioni.append(["2", "2", "1", "1", data_base+"11", data_base+"15"])
    prenotazioni.append(["3", "3", "1", "1", data_base+"21", data_base+"25"])
    prenotazioni.append(["4", "9", "2", "1", data_base+"14", data_base+"19"])
    prenotazioni.append(["5", "5", "3", "1", data_base+"7", data_base+"9"])
    prenotazioni.append(["6", "6", "3", "1", data_base+"14", data_base+"16"])
    prenotazioni.append(["7", "6", "3", "1", data_base+"28", data_base+"30"])
    prenotazioni.append(["8", "7", "4", "1", data_base+"4", data_base+"7"])
    prenotazioni.append(["9", "9", "4", "1", data_base+"19", data_base+"22"])
    prenotazioni.append(["10", "4", "6", "1", data_base+"1", data_base+"30"])
    prenotazioni.append(["11", "9", "7", "1", data_base+"4", data_base+"7"])
    prenotazioni.append(["12", "10", "8", "1", data_base+"10", data_base+"13"])
    prenotazioni.append(["13", "11", "8", "1", data_base+"19", data_base+"23"])
    prenotazioni.append(["14", "12", "9", "1", data_base+"6", data_base+"9"])
    prenotazioni.append(["15", "13", "9", "1", data_base+"19", data_base+"22"])
    prenotazioni.append(["16", "12", "9", "1", data_base+"24", data_base+"26"])
    prenotazioni.append(["17", "14", "10", "1", data_base+"2", data_base+"5"])
    prenotazioni.append(["18", "14", "10", "1", data_base+"13", data_base+"17"])
    write_on_csv(prenotazioni_header, prenotazioni, "prenotazioni.csv")

def generate_commenti():
    comments_header = ["prenotazione", "data_pubblicazione", "titolo", "commento", "votazione"]
    next_data_base = "2020-02-10 09:21:00" #da modifcare in caso di cambiamenti
    for prenotazione in prenotazioni:
        new_commento = [prenotazione[0], next_data_base, "Soggiorno molto bello".format(prenotazione[0]), "Il soggiorno è stato molto piacevole, consiglio assolutamente la visita", str(random.randrange(3, 5))]
        commenti.append(new_commento)
    write_on_csv(comments_header, commenti, "commenti.csv")


generate_users()
generate_annunci()
generate_prenotazioni()
generate_commenti()
