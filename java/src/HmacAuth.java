import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.TimeZone;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class HmacAuth {
	private String algorithm, timestamp, key, secret;
	
	/**
	 * Constructor
	 * 
	 * @param key - API Key
	 * @param secret - API Secret
	 * @param algorithm - HMAC algorithm to use (HmacSHA1 or HmacSHA256)
	 */
	public HmacAuth(String key, String secret, String algorithm) {
		this.algorithm = algorithm;
		this.timestamp = getUTCdatetimeAsString();
		this.key = key;
		this.secret = secret;
	}
	
	/**
	 * Creates the Auth HMAC hash for x-hp-hmac-authentication header
	 * 
	 * @param method - http method (GET, POST, PUT)
	 * @param path - api path 
	 * @return returns the auth hash for x-hp-hmac-authentication
	 * @throws NoSuchAlgorithmException
	 * @throws InvalidKeyException
	 */
	public String getHmacAuthentication(String method, String path) throws NoSuchAlgorithmException, InvalidKeyException {
	    if( (!algorithm.equals("HmacSHA1")) && (!algorithm.equals("HmacSHA256")) ) 
	    	throw new NoSuchAlgorithmException();
	    
	    Mac mac = Mac.getInstance(algorithm);

	    SecretKeySpec secret_key = new SecretKeySpec(secret.getBytes(), algorithm);
	    mac.init(secret_key);

	    String toHash = method + " " + path + timestamp;
	    byte[] rawHmac = mac.doFinal(toHash.getBytes());

	    String hash = encodeHex(rawHmac);
	    return key + ":" + hash;
	}
	
	/**
	 * @return time for x-hp-hmac-date header
	 */
	public String getTimestamp() {
		return timestamp;
	}
	
    /**
     * encodes a byte array into a hex string
     * 
     * @param a - resulting byte array of a hmac hash
     * @return
     */
    private static String encodeHex(byte[] a) {
        StringBuilder sb = new StringBuilder(a.length * 2);
        for (byte b : a) {
            sb.append(String.format("%02x", b & 0xff));
        }
        return sb.toString();
    }
	
    /**
     * @return current time in UTC format
     */
    private static String getUTCdatetimeAsString(){
        final String DATE_FORMAT = "yyyy-MM-dd'T'HH:mm:ss.sss'Z'";// e.g.: 2016-04-15T12:00:00.000Z
        final SimpleDateFormat sdf = new SimpleDateFormat(DATE_FORMAT);
        sdf.setTimeZone(TimeZone.getTimeZone("UTC"));
        final String utcTime = sdf.format(new Date());
        return utcTime;
    }
}
