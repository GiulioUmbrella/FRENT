from sys import exit, argv
import csv
import random


user_data = []
annunci_data = []
occupazioni = []
commenti = []
data_base = "2019-11-"

def write_on_csv(header, csv_data, file_path="data.csv"):
    with open(file_path, 'w') as csv_file:
        writer = csv.writer(csv_file)
        writer.writerow(header)
        writer.writerows(csv_data)


def generate_users():
    #15 Nomi
    names = ["Jolanda","Melchiorre","Cirino","Ignazio","Elio","Gianni","Amore","Marisa","Dania","Alberico","Lodovico","Marino","Nella","Pompeo","Alfonso"]
    surnames = ["Rossi","Ferrari","Russo","Bianchi","Romano","Gallo","Costa","Fontana","Conti","Esposito","Ricci","Bruno","Rizzo","Moretti","Marino"]
    default_image = "user_image.png"
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
        user_data.append([str(i+1), names[i], surnames[i], emails[i], usernames[i], passwords[i], date_di_nascita[i], default_image, telefoni[i]])

    user_header = ["id_utente", "nome", "cognome", "user_name", "mail", "password", "data_nascita", "img_profilo", "telefono"]
    write_on_csv(user_header, user_data, "utenti.csv")
    #print(csv_data)

def generate_annunci():
    #numero_annunci = 10
    titoli = ["Casa {}".format(n) for n in ["Loreto","Agrippa","Celeste","Aquila","Amore"]]
    titoli += ["Appartamento {}". format(n) for n in ["Aran","Flann","Glaucia","Nollaig","Amore"]]

    descrizioni = ["defualt descirptio" for _ in titoli]
    img_anteprima = ["images/annunci/anteprima_{}".format(t) for t in titoli]

    if len(argv) > 1:
        if argv[1] == "--less-random":
            with open("vie.csv", 'r') as vie_file:
                random.seed(42)
                readCSV = [row for row in csv.reader(vie_file, delimiter=',')]
                indirizzi = [str(readCSV[random.randrange(1, len(readCSV))][2]) for _ in titoli]
                citta = ["roma" for _ in titoli]
            hosts = [str(user_data[random.randrange(0, len(user_data)-1)][0]) for _ in titoli]
            stato_approvazione = ["1" for _ in titoli]
            bloccato = ["0" for _ in titoli]
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
        bloccato = [str(random.randrange(0, 1)) for _ in titoli]
        max_ospiti = [str(random.randrange(1, 7)) for _ in titoli]
        prezzo_notte = [str(random.randrange(25, 150)) for _ in titoli]

    for i in range(len(titoli)):
        annunci_data.append([
            str(i+1),
            titoli[i],
            descrizioni[i],
            img_anteprima[i],
            indirizzi[i],
            citta[i],
            hosts[i],
            stato_approvazione[i],
            bloccato[i],
            max_ospiti[i],
            prezzo_notte[i],
        ])

    annunci_header = ["id_annuncio", "titolo", "descrizione", "img_anteprima", "indirizzo", "citta", "host", "stato_approvazione", "bloccato", "max_ospiti", "prezzo_notte"]
    write_on_csv(annunci_header, annunci_data, "annunci.csv")


def generate_occupazioni():

    occupazioni_header = ["id_occupazione", "utente", "annuncio", "prenotazione_guest", "num_ospiti", "data_inizio", "data_fine"]
    occupazioni.append(["1", "1", "1", "1", "1", data_base+"2", data_base+"7"])
    occupazioni.append(["2", "2", "1", "1", "1", data_base+"11", data_base+"15"])
    occupazioni.append(["3", "3", "1", "1", "1", data_base+"21", data_base+"25"])
    occupazioni.append(["4", "9", "2", "0", "1", data_base+"14", data_base+"19"])
    occupazioni.append(["5", "5", "3", "1", "1", data_base+"7", data_base+"9"])
    occupazioni.append(["6", "6", "3", "1", "1", data_base+"14", data_base+"16"])
    occupazioni.append(["7", "6", "3", "1", "1", data_base+"28", data_base+"30"])
    occupazioni.append(["8", "7", "4", "1", "1", data_base+"4", data_base+"7"])
    occupazioni.append(["9", "9", "4", "1", "1", data_base+"19", data_base+"22"])
    occupazioni.append(["10", "4", "6", "0", "1", data_base+"1", data_base+"30"])
    occupazioni.append(["11", "9", "7", "1", "1", data_base+"4", data_base+"7"])
    occupazioni.append(["12", "10", "8", "1", "1", data_base+"10", data_base+"13"])
    occupazioni.append(["13", "11", "8", "0", "1", data_base+"19", data_base+"23"])
    occupazioni.append(["14", "12", "9", "0", "1", data_base+"6", data_base+"9"])
    occupazioni.append(["15", "13", "9", "1", "1", data_base+"19", data_base+"22"])
    occupazioni.append(["16", "12", "9", "0", "1", data_base+"24", data_base+"26"])
    occupazioni.append(["17", "14", "10", "1", "1", data_base+"2", data_base+"5"])
    occupazioni.append(["18", "14", "10", "1", "1", data_base+"13", data_base+"17"])
    write_on_csv(occupazioni_header, occupazioni, "occupazioni.csv")

def generate_commnenti():
    comments_header = ["prenotazione", "data_pubblicazione", "titolo", "commento", "votazione"]
    next_data_base = "2019-12-01 09:21:00" #da modifcare in caso di cambiamenti
    for occupazione in occupazioni:
        new_commento = [occupazione[0], next_data_base, "Titolo per l'occupazioni di id {}".format(occupazione[0]), "Commento molto molto ma vermanete molto lungo relativo all'occupazione di id {}".format(occupazione[0]), str(random.randrange(1, 5))]
        commenti.append(new_commento)
    write_on_csv(comments_header, commenti, "commenti.csv")


generate_users()
generate_annunci()
generate_occupazioni()
generate_commnenti()
