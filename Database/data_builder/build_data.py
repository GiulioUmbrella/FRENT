from sys import exit
import csv

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

    csv_data = []
    for i in range(len(names)):
        csv_data.append([str(i+1), names[i], surnames[i], emails[i], usernames[i], passwords[i], date_di_nascita[i], default_image, telefoni[i]])

    user_header = ["id_utente", "nome", "cognome", "user_name", "mail", "password", "data_nascita", "img_profilo", "telefono"]
    write_on_csv(user_header, csv_data, "utenti.csv")
    #print(csv_data)

def generate_annunci():
    pass


def generate_occupazioni():
    pass


generate_users()
