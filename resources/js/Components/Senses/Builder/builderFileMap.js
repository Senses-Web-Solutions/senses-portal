export default function(file) {
    file.size = file.size ? file.size / 1000000 : 0; //hardcoded for now, odd things were done here
    file.name = file.display_name;
    file.preview_url = file.url;
    file.download_url = file.url;
    file.extension = file.file_type;
    file.subtitle = " ";
    return file;
}