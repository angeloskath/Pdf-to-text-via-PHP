PDF 2 text via PHP
-------------------

This is a collection (quite a draft collection) of classes with the aspiration of becoming a parsing library so that the task of converting a pdf file to text or extracting its images through php becomes a trivial one.

Sharing
-------

Sharing is highly appreciated. The current code is by no means a working/stable library, but it does work quite well with pdfs exported with OpenOffice even with unicode characters.

Internals
---------

There is an encapsulating *Pdf* class that parses the document, quite ignorantly at the moment due to poor reading of the [pdf reference](http://wwwimages.adobe.com/www.adobe.com/content/dam/Adobe/en/devnet/pdf/pdfs/PDF32000_2008.pdf), and then creates all the pdf objects that it meets.

The classes do not declare an object type for each possible pdf value because php already covers the value types boolean, string, int, float, etc. but they do declare a *PdfStream* (which is our main interest) and a *PdfReferenceValue*.

### PdfStream

The pdf stream represents a stream of information encapsulated in the pdf keywords 'stream' 'endstream'. It allows for easy decoding (decompressing, etc.) and it determines if it is a text stream statistically measuring the BT pdf keywords of the decompressed stream.

If it is, indeed, a string it passes it to the *PdfstringParser* in order to apply the correct unicode mappings and return a utf-8 string.

### PdfReferenceValue

As its name show this class is simply a reference to the value of another object.
